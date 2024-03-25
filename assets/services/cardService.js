export async function fetchAllCards() {
    const response = await fetch('/api/card/all');
    if (!response.ok) throw new Error('Failed to fetch cards');
    return await response.json();
}

export async function fetchAllCardsWithPagination(page = 1, size = 100) {
    const response = await fetch(`/api/card/all/${page}/${size}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const data = await response.json();
    return {
        cards: data.cards,
        totalPages: Math.ceil(data.total / size),
    };
}

export async function fetchCardsByName(name) {
    const response = await fetch(`/api/card/search/${name}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    return await response.json();
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchSetCode() {
    const response = await fetch('/api/card/set-codes');
    if (!response.ok) throw new Error('Failed to fetch set code');
    return await response.json();
}

export async function fetchCardsBySetCode(setCode, page = 1, size = 10) {
    const response = await fetch(`/api/card/set-code/${setCode}/${page}/${size}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const data = await response.json();
    return {
        cards: data.cards,
        totalPages: Math.ceil(data.total / size),
    };
}