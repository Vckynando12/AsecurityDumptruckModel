<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class CardController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $cards = $this->database->getReference('registered_cards/list')->getValue();
        $total = $this->database->getReference('registered_cards/total')->getValue();
        $lastCardId = $this->database->getReference('smartcab/status_device')->getValue();
        $lastAccess = $this->database->getReference('smartcab/last_access')->getValue();
        
        return view('view_card', compact('cards', 'total', 'lastCardId', 'lastAccess'));
    }

    public function addCard(Request $request)
    {
        $request->validate([
            'card_id' => 'required|string|min:7|max:8',
        ]);

        $cardId = $request->input('card_id');
        $this->database->getReference('registered_cards/card')->set($cardId);
        
        // Wait for 1.5 seconds before resetting
        usleep(1500000);
        
        // Reset the card value to empty string after sending command
        $this->database->getReference('registered_cards/card')->set('');
        
        return redirect()->route('view.cards')->with('success', 'Card added successfully!');
    }

    public function deleteCard($cardId)
    {
        $this->database->getReference('registered_cards/delete')->set($cardId);
        
        // Wait for 1.5 seconds before resetting
        usleep(1500000);
        
        // Reset the delete value to empty string after sending command
        $this->database->getReference('registered_cards/delete')->set('');
        
        return redirect()->route('view.cards')->with('success', 'Card deletion requested');
    }

    public function deleteAllCards()
    {
        $this->database->getReference('registered_cards/delete')->set('all');
        
        // Wait for 1.5 seconds before resetting
        usleep(1500000);
        
        // Reset the delete value to empty string after sending command
        $this->database->getReference('registered_cards/delete')->set('');
        
        return redirect()->route('view.cards')->with('success', 'All cards deletion requested');
    }
} 