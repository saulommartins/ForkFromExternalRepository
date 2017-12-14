<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Componente para informar os atributos de entrada de um ítem
    * Data de Criação: 01/04/2008

    * @author Andre Almeida

    * Casos de uso: uc-03.03.16

    $Id: $

*/

include_once( CLA_OBJETO );
include_once '../../instancias/processamento/OCIMontaAtributosEntrada.php';

class  IMontaAtributosEntrada extends Objeto
{

    public function setIdCampoQuantidadeTotal($stIdCampoQuantidadeTotal) { $this->stIdCampoQuantidadeTotal = $stIdCampoQuantidadeTotal; }

    public function IMontaAtributosEntrada( $inCodItem, $arAtributosValor = array() )
    {
        parent::Objeto();

        Sessao::remove('atributosNulo');

        include_once ( TALM."TAlmoxarifadoAtributoCatalogoItem.class.php" );
        $pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaAtributosEntrada.php?'.Sessao::getId();
        Sessao::write('IMontaAtributosEntradaValores', $arAtributosValor);

        $obTAlmoxarifadoAtributoCatalogoItem = new TAlmoxarifadoAtributoCatalogoItem;
        $obTAlmoxarifadoAtributoCatalogoItem->setDado( 'cod_item', $inCodItem );
        $obTAlmoxarifadoAtributoCatalogoItem->recuperaAtributoCatalogoItem( $rsAtributos, "" ,
                                                                           " AND atributo_dinamico.cod_tipo != 3 ".
                                                                           " AND atributo_dinamico.cod_tipo != 4 ".
                                                                           " ORDER BY atributo_dinamico.nom_atributo " );
        $obTAlmoxarifadoAtributoCatalogoItem->recuperaAtributoDinamicoItem( $rsAtributosLista, " AND TA.cod_tipo = 3 ORDER BY AD.nom_atributo" );
        $obTAlmoxarifadoAtributoCatalogoItem->recuperaAtributoDinamicoItem( $rsAtributosListaMult, " AND TA.cod_tipo = 4 ORDER BY AD.nom_atributo" );

        $this->obHdnInId = new Hidden;
        $this->obHdnInId->setName("inId");
        $this->obHdnInId->setId("inId");

        $arAtributos          = $rsAtributos->arElementos;
        $arAtributosLista     = $rsAtributosLista->arElementos;
        $arAtributosListaMult = $rsAtributosListaMult->arElementos;

        $this->arObTxtAtributo = array();
        for ( $i=0; $i<count($arAtributos); $i++ ) {
            $stAtributo = "stAtributo_".$arAtributos[$i]["nom_atributo"];

            switch ($arAtributos[$i]['cod_tipo']) {
            //case 'Numerico':
            case '1':
                $this->arObTxtAtributo[$i] = new TextBox;
                $this->arObTxtAtributo[$i]->setName             ( $stAtributo );
                $this->arObTxtAtributo[$i]->setId               ( $stAtributo );
                $this->arObTxtAtributo[$i]->setRotulo           ( $arAtributos[$i]["nom_atributo"] );
                $this->arObTxtAtributo[$i]->setInteiro          ( true );
                $this->arObTxtAtributo[$i]->setSize             ( 20 );
                $this->arObTxtAtributo[$i]->setMaxLength        ( 500 );

                if ($arAtributos[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributo[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributos[$i]["nom_atributo"]]= true;
                }

                if ($arAtributos[$i]['valor_padrao'] == "") {
                    $stAtributoValor = "";
                } else {
                    $stAtributoValor = $arAtributos[$i]['valor_padrao'];
                }
                $this->arObTxtAtributo[$i]->setValue            ( $stAtributoValor );
            break;

            //case 'Texto':
            case '2':
                $this->arObTxtAtributo[$i] = new TextBox;
                $this->arObTxtAtributo[$i]->setName( $stAtributo );
                $this->arObTxtAtributo[$i]->setId( $stAtributo );
                $this->arObTxtAtributo[$i]->setRotulo( $arAtributos[$i]["nom_atributo"] );
                $this->arObTxtAtributo[$i]->setInteiro      (false);
                $this->arObTxtAtributo[$i]->setSize         ( 30 );
                $this->arObTxtAtributo[$i]->setMaxLength    ( 500 );

                if ($arAtributos[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributo[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributos[$i]["nom_atributo"]]= true;
                }

                if ($arAtributos[$i]['valor_padrao'] == "") {
                    $stAtributoValor = "";
                } else {
                    $stAtributoValor = $arAtributos[$i]['valor_padrao'];
                }
                $this->arObTxtAtributo[$i]->setValue            ( $stAtributoValor );
                break;

            //case 'Data':
            case '5':
                $this->arObTxtAtributo[$i] = new Data;
                $this->arObTxtAtributo[$i]->setName( $stAtributo);
                $this->arObTxtAtributo[$i]->setId( $stAtributo );
                $this->arObTxtAtributo[$i]->setRotulo( $arAtributos[$i]["nom_atributo"] );

                if ($arAtributos[$i]['nao_nulo'] == 'f') {
                    $this->arObTxtAtributo[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributos[$i]["nom_atributo"]]= true;
                }

                if ($arAtributos[$i]['valor_padrao'] == "") {
                    $stAtributoValor = "";
                } else {
                    $stAtributoValor = $arAtributos[$i]['valor_padrao'];
                }
                $this->arObTxtAtributo[$i]->setValue            ( $stAtributoValor );
                break;

            //case 'Numerico (*,2)':
            case '6':
                $this->arObTxtAtributo[$i] = new TextBox;
                $this->arObTxtAtributo[$i]->setName( $stAtributo );
                $this->arObTxtAtributo[$i]->setId( $stAtributo );
                $this->arObTxtAtributo[$i]->setRotulo( $arAtributos[$i]["nom_atributo"] );
                $this->arObTxtAtributo[$i]->setFloat        (true);
                $this->arObTxtAtributo[$i]->setSize         ( 20 );
                $this->arObTxtAtributo[$i]->setMaxLength    ( 500 );

                if ($arAtributos[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributo[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributos[$i]["nom_atributo"]]= true;
                }

                if ($arAtributos[$i]['valor_padrao'] == "") {
                    $stAtributoValor = "";
                } else {
                    $stAtributoValor = $arAtributos[$i]['valor_padrao'];
                }
                $this->arObTxtAtributo[$i]->setValue            ( $stAtributoValor );
                break;

            //case 'Texto Longo':
            case '7':
                $this->arObTxtAtributo[$i] = new TextArea();
                $this->arObTxtAtributo[$i]->setName( $stAtributo );
                $this->arObTxtAtributo[$i]->setId( $stAtributo );
                $this->arObTxtAtributo[$i]->setRotulo( $arAtributos[$i]["nom_atributo"] );
                $this->arObTxtAtributo[$i]->setMaxCaracteres( 1000 );

                if ($arAtributos[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributo[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributos[$i]["nom_atributo"]]= true;
                }

                if ($arAtributos[$i]['valor_padrao'] == "") {
                    $stAtributoValor = "";
                } else {
                    $stAtributoValor = $arAtributos[$i]['valor_padrao'];
                }
                $this->arObTxtAtributo[$i]->setValue            ( $stAtributoValor );
                break;

            }

            $stParamsAtributos .= "&stAtributo[".$arAtributos[$i]["nom_atributo"]."]='+document.getElementById('stAtributo_".$arAtributos[$i]["nom_atributo"]."').value+'";
        }

        //'Lista':
        $arValorPadraoTMP = array();
        $this->arObTxtAtributoLista = array();
        if ($rsAtributosLista->getNumLinhas()>0) {

            for ( $i=0; $i<count($arAtributosLista); $i++ ) {
                if ($arAtributosLista[$i]['valor_padrao']) {
                    //$arValorPadrao      = explode("[][][]" , $arAtributosLista[$i]['valor_padrao_desc']);
                    $arValorPadrao      = explode(",", str_replace(' ','',trim($arAtributosLista[$i]['valor_padrao'])));
                    $arValorPadraoDesc  = explode("[][][]" , $arAtributosLista[$i]['valor_padrao_desc'] );
                    foreach ($arValorPadrao as $key=>$value) {
                        $arValorPadraoTMP[$key]['inCodValor']  = $arValorPadrao[$key];
                        $arValorPadraoTMP[$key]['stDescValor'] = $arValorPadraoDesc[$key];
                    }
                } else {
                    $arValorPadraoTMP = array();
                }
                $rsValorPadrao = new RecordSet;
                $rsValorPadrao->preenche($arValorPadraoTMP);
                $rsValorPadrao->ordena('stDescValor');

                $this->arObTxtAtributoLista[$i] = new Select;
                $this->arObTxtAtributoLista[$i]->setName         ( "stAtributo_".$arAtributosLista[$i]["nom_atributo"] );
                $this->arObTxtAtributoLista[$i]->setId           ( "stAtributo_".$arAtributosLista[$i]["nom_atributo"] );
                $this->arObTxtAtributoLista[$i]->setValue        ( $stAtributoValor );
                $this->arObTxtAtributoLista[$i]->setRotulo       ( $arAtributosLista[$i]["nom_atributo"] );
                $this->arObTxtAtributoLista[$i]->setTitle        ( $arAtributosLista[$i]["nom_atributo"] );
                $this->arObTxtAtributoLista[$i]->addOption       ( "", "Selecione" );
                $this->arObTxtAtributoLista[$i]->setStyle        ( "width: 200px");
                $this->arObTxtAtributoLista[$i]->setCampoID      ( "inCodValor" );
                $this->arObTxtAtributoLista[$i]->setCampoDesc    ( "stDescValor" );

                if ($arAtributosLista[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributoLista[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$arAtributosLista[$i]["nom_atributo"]]= true;
                }
                $this->arObTxtAtributoLista[$i]->preencheCombo   ($rsValorPadrao);

                //$stParamsAtributos .= "&stAtributo[".$arAtributosLista[$i]["nom_atributo"]."][valor]='+document.getElementById('stAtributo_".$arAtributosLista[$i]["nom_atributo"]."').value+'&stAtributo[".$arAtributosLista[$i]["nom_atributo"]."][texto]='+document.getElementById('stAtributo_".$arAtributosLista[$i]["nom_atributo"]."').text+'";
                $stParamsAtributos .= "&stAtributo[".$arAtributosLista[$i]["nom_atributo"]."][".$i."][valor]='+document.getElementById('stAtributo_".$arAtributosLista[$i]["nom_atributo"]."').value+'&stAtributo[".$arAtributosLista[$i]["nom_atributo"]."][".$i."][texto]='+document.getElementById('stAtributo_".$arAtributosLista[$i]["nom_atributo"]."').options[document.getElementById('stAtributo_".$arAtributosLista[$i]["nom_atributo"]."').selectedIndex].text+'";
            }
        }
        //'Lista Múltipla':
        if ($rsAtributosListaMult->getNumLinhas()>0) {
            for ( $i=0; $i<count($arAtributosListaMult); $i++ ) {

                $arValorPadraoTMP = array();
                if ($arAtributosListaMult[$i]['valor_padrao']) {
                    $arValorPadrao      = explode(","      , $arAtributosListaMult[$i]['valor_padrao'] );
                    //$arValorPadrao      = explode("[][][]"      , $arAtributosListaMult[$i]['valor_padrao_desc'] );
                    $arValorPadraoDesc  = explode("[][][]" , $arAtributosListaMult[$i]['valor_padrao_desc']  );
                    foreach ($arValorPadrao as $key=>$value) {
                        $arValorPadraoTMP[$key]['inCodValor']  = $arValorPadrao[$key];
                        $arValorPadraoTMP[$key]['stDescValor'] = $arValorPadraoDesc[$key];
                    }
                }
                $arValorTMP = array();
                if ($arAtributosListaMult[$i]['valor']) {
                    $arValor      = explode("[][][]"      , $arAtributosListaMult[$i]['valor_desc'] );
                    $arValorDesc  = explode("[][][]" , $arAtributosListaMult[$i]['valor_desc'] );
                    foreach ($arValor as $key=>$value) {
                        $arValorTMP[$key]['inCodValor']  = $arValor[$key];
                        $arValorTMP[$key]['stDescValor'] = $arValorDesc[$key];
                    }
                }

                $rsValorPadrao = new RecordSet;
                $rsValorPadrao->preenche($arValorPadraoTMP);
                $rsValorPadrao->ordena('stDescValor');
                $rsValor = new RecordSet;
                $rsValor->preenche($arValorTMP);
                $rsValor->ordena('stDescValor');

                $nomeAtributo = str_replace(' ','',trim($arAtributosListaMult[$i]["nom_atributo"]));

                if ($atributosListaMultiplaId != "") {
                    $atributosListaMultiplaId.= " | ";
                }
                $atributosListaMultiplaId.= "stAtributo_".$nomeAtributo."_Selecionados";

                if ($atributosListaMultiplaNome != "") {
                    $atributosListaMultiplaNome.= " | ";
                }
                $atributosListaMultiplaNome.= $nomeAtributo;

                $this->arObTxtAtributoListaMult[$i] = new SelectMultiplo();
                $this->arObTxtAtributoListaMult[$i]->setName         ( "stAtributo_".$nomeAtributo );
                $this->arObTxtAtributoListaMult[$i]->setValorPadrao  ( " " );
                $this->arObTxtAtributoListaMult[$i]->setRotulo       ( $arAtributosListaMult[$i]["nom_atributo"] );
                $this->arObTxtAtributoListaMult[$i]->setTitle        ( $arAtributosListaMult[$i]["nom_atributo"] );

                if ($arAtributosListaMult[$i]['nao_nulo']=='f') {
                    $this->arObTxtAtributoListaMult[$i]->setObrigatorioBarra(true);
                    $atributoNulo[$nomeAtributo."_Selecionados"]['naoNulo']= true;
                    $atributoNulo[$nomeAtributo."_Selecionados"]['nome']= $arAtributosListaMult[$i]["nom_atributo"];
                }

                // lista de atributos disponiveis
                $this->arObTxtAtributoListaMult[$i]->SetNomeLista1 ( "stAtributo_".$nomeAtributo.'_Disponiveis');
                $this->arObTxtAtributoListaMult[$i]->setCampoId1   ('inCodValor');
                $this->arObTxtAtributoListaMult[$i]->setCampoDesc1 ('stDescValor');
                $this->arObTxtAtributoListaMult[$i]->SetRecord1    ( $rsValorPadrao );
                // lista de atributos selecionados
                $this->arObTxtAtributoListaMult[$i]->SetNomeLista2 ( "stAtributo_".$nomeAtributo."_Selecionados");
                $this->arObTxtAtributoListaMult[$i]->setCampoId2   ('inCodValor');
                $this->arObTxtAtributoListaMult[$i]->setCampoDesc2 ('stDescValor');
                $this->arObTxtAtributoListaMult[$i]->SetRecord2    ( $rsValor );

            }
        }

        $this->obTxtQuantidade = new Quantidade;
        $this->obTxtQuantidade->setRotulo  ( "Quantidade" );
        $this->obTxtQuantidade->setName    ( "nuQuantidadeAtributo" );
        $this->obTxtQuantidade->setId      ( "nuQuantidadeAtributo" );
        $this->obTxtQuantidade->setInteiro ( false );
        $this->obTxtQuantidade->setFloat   ( true  );
        $this->obTxtQuantidade->setObrigatorioBarra( true );

        $stParamsAtributos .= "&nuQuantidadeAtributo='+document.getElementById('nuQuantidadeAtributo').value";

        $this->obBtnIncluir = new Button;
        $this->obBtnIncluir->setName              ( "btIncluirAtributos"    );
        $this->obBtnIncluir->setId                ( "btIncluirAtributos"    );
        $this->obBtnIncluir->setValue             ( "Incluir"             );
        $this->obBtnIncluir->obEvento->setOnClick ("selecionaValoresAtributos('".$pgOc.$stParamsAtributos.",'incluirAtributos', '".$atributosListaMultiplaId."' , '".$atributosListaMultiplaNome."');");

        $this->obBtnAlterar = new Button;
        $this->obBtnAlterar->setName              ( "btAlterarAtributos"    );
        $this->obBtnAlterar->setId                ( "btAlterarAtributos"    );
        $this->obBtnAlterar->setValue             ( "Alterar"             );
        $this->obBtnAlterar->setDisabled(True);
        $this->obBtnAlterar->obEvento->setOnClick ("selecionaValoresAtributos('".$pgOc."&inId='+document.getElementById('inId').value+'".$stParamsAtributos.",'alterarAtributos', '".$atributosListaMultiplaId."' , '".$atributosListaMultiplaNome."');");

        $this->obBtnLimpar = new Button;
        $this->obBtnLimpar->setName              ( "btLimparAtributos"          );
        $this->obBtnLimpar->setValue             ( "Limpar"                   );
        $this->obBtnLimpar->obEvento->setOnClick ("selecionaValoresAtributos('".$pgOc.$stParamsAtributos.",'limparCamposAtributo', '".$atributosListaMultiplaId."' , '".$atributosListaMultiplaNome."');");

        $this->spnListaAtributos = new Span;
        $this->spnListaAtributos->setId( "spnListaAtributos" );
        $this->spnListaAtributos->setValue( montaHTMLListaAtributos(false) );

        Sessao::write('atributosNulo',$atributoNulo);
    }

    public function possuiAtributos()
    {
        if ( ( count($this->arObTxtAtributo) > 0 ) || ( count($this->arObTxtAtributoListaMult)> 0) || ( count($this->arObTxtAtributoLista)> 0) ) {
            return true;
        } else {
            return false;
        }
    }

    public function geraFormulario(&$obFormulario)
    {
        if ( $this->possuiAtributos() ) {
            $obFormulario->addTitulo('Atributos de Entrada');
            $obFormulario->addHidden( $this->obHdnInId );
            for ( $i=0; $i<count($this->arObTxtAtributo); $i++ ) {
                $obFormulario->addComponente( $this->arObTxtAtributo[$i] );
            }

            if ($this->arObTxtAtributoLista) {
                for ( $i=0; $i<count($this->arObTxtAtributoLista); $i++ ) {
                 $obFormulario->addComponente( $this->arObTxtAtributoLista[$i] );
                }
            }

            if ($this->arObTxtAtributoListaMult) {
                for ( $i=0; $i<count($this->arObTxtAtributoListaMult); $i++ ) {
                    $obFormulario->addComponente( $this->arObTxtAtributoListaMult[$i] );
                }
            }

            $obFormulario->addComponente( $this->obTxtQuantidade );
            $obFormulario->defineBarra( array($this->obBtnIncluir, $this->obBtnAlterar, $this->obBtnLimpar), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
            $obFormulario->addSpan( $this->spnListaAtributos );
            Sessao::write('IMontaAtributosEntrada', $this);
        }
    }
}
