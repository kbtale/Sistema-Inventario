<script>
  import { onMount, createEventDispatcher } from "svelte";
  import L from "leaflet";
  import "leaflet/dist/leaflet.css";
  import "leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.css";
  import "leaflet-defaulticon-compatibility";
  import { api } from "./api";

  const dispatch = createEventDispatcher();
  let map;
  let mapContainer;
  let locations = [];
  let loading = true;

  onMount(async () => {
    try {
      locations = await api.getSedeDistribution();
      initMap();
    } catch (e) {
      console.error("Map data load failed", e);
    } finally {
      loading = false;
    }
  });

  function initMap() {
    if (!mapContainer) return;

    // Default center
    map = L.map(mapContainer, {
      zoomControl: false,
      attributionControl: false,
    }).setView([10.49, -66.86], 12);

    // Modern light-themed tile layer
    L.tileLayer(
      "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
      {
        maxZoom: 19,
      },
    ).addTo(map);

    L.control.zoom({ position: "bottomright" }).addTo(map);

    locations.forEach((loc) => {
      if (loc.lat && loc.lng) {
        const marker = L.marker([
          parseFloat(loc.lat),
          parseFloat(loc.lng),
        ]).addTo(map);

        const popupContent = `
          <div class="map-popup">
            <h4 style="margin: 0 0 8px 0; color: #1a1a2e;">${loc.name}</h4>
            <div style="font-size: 12px; margin-bottom: 10px; color: #666;">
              <strong>Hardware:</strong> ${loc.hardware_count}<br/>
              <strong>Mobile:</strong> ${loc.mobile_count}
            </div>
            <button class="btn-filter" id="filter-${loc.id_sede}">Filter This Sede</button>
          </div>
        `;

        marker.bindPopup(popupContent, {
          className: "custom-leaflet-popup",
        });

        marker.on("popupopen", () => {
          const btn = document.getElementById(`filter-${loc.id_sede}`);
          if (btn) {
            btn.onclick = () => {
              dispatch("filter", { id: loc.id_sede, name: loc.name });
              map.closePopup();
            };
          }
        });
      }
    });
  }
</script>

<div class="map-wrapper card" class:loading>
  {#if loading}
    <div class="map-loader">
      <div class="spinner"></div>
      <p>Initializing spatial engine...</p>
    </div>
  {/if}
  <div bind:this={mapContainer} class="map-instance"></div>
</div>

<style>
  .map-wrapper {
    height: 420px;
    margin-bottom: var(--space-xl);
    padding: 0;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .map-instance {
    height: 100%;
    width: 100%;
    z-index: 1;
    background: #f8f9fa;
  }

  .map-loader {
    position: absolute;
    inset: 0;
    z-index: 2;
    background: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--color-primary);
    font-size: 14px;
    gap: 15px;
  }

  .spinner {
    width: 30px;
    height: 30px;
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-top: 3px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  :global(.custom-leaflet-popup .leaflet-popup-content-wrapper) {
    border-radius: 12px;
    padding: 6px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }

  :global(.map-popup) {
    font-family: inherit;
    min-width: 140px;
  }

  :global(.btn-filter) {
    width: 100%;
    background: var(--color-primary);
    color: white;
    border: none;
    padding: 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  :global(.btn-filter:hover) {
    filter: brightness(1.2);
    transform: translateY(-1px);
  }
</style>
