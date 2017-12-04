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
    * Página Oculta de Funções
    * Data de Criação   : 30/01/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-03.03.14

    $Id: OCManterLocalizacao.php 61639 2015-02-19 13:05:36Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoLocalizacao.class.php"                              );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarifado.class.php"                             );
$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$stPrograma = "ManterItem";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obRegra = new RAlmoxarifadoLocalizacao;
if (isset($stCtrl) && $stCtrl != null) {
    
    switch ($stCtrl) {

    //Carrega dados do Arquivo FMManterLocalizacaoItem.php
    case "Localizacao" :

     $rsAlmoxarifado             = new Recordset;
     $rsLocalizacao              = new Recordset;
     $obRAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao;

     $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
     $inCodItem         = $_REQUEST['inCodItem'];
     $inCodMarca        = $_REQUEST['inCodMarca'];

     $obRAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->setCodigo($inCodAlmoxarifado);

     if (!($inCodItem == "" && $inCodMarca == "")) {
      $obRAlmoxarifadoLocalizacao->obRAlmoxarifadoItemMarca->obRCatalogoItem->setCodigo($inCodItem);
      $obRAlmoxarifadoLocalizacao->obRAlmoxarifadoItemMarca->obRMarca->setCodigo($inCodMarca);
     }
     if ($inCodAlmoxarifado != "") {
       $obRegra = $obRAlmoxarifadoLocalizacao->listar($rsAlmoxarifado);

       $i                 = 1;
       $inItemLocalizacao = 0;

       if (!($obRegra->ocorreu())) {
         $stJs = "limpaSelect(f.inCodLocalizacao,1);                       \n";
         $stJs.= "f.inCodLocalizacao.options[0] = new Option('Selecione','');";
         if ($inCodItem != "" & $inCodMarca != "") {

           $obRAlmoxarifadoLocalizacao->addLocalizacaoItem();
           $obRAlmoxarifadoLocalizacao->roLocalizacaoItem->obRCatalogoItem->setCodigo($inCodItem);
           $obRAlmoxarifadoLocalizacao->roLocalizacaoItem->obRMarca->setCodigo($inCodMarca);

           $obErro = $obRAlmoxarifadoLocalizacao->listarItens($rsLocalizacao);

            if (!$obErro->ocorreu()) {
             $inItemLocalizacao = $rsLocalizacao->getCampo("cod_localizacao");
            }

         }
           while (!($rsAlmoxarifado->EOF())) {
            $stJs.="f.inCodLocalizacao.options[".$i."] = new Option('".$rsAlmoxarifado->getCampo("localizacao")."',".$rsAlmoxarifado->getCampo("cod_localizacao").");";

               if ($inItemLocalizacao == $rsAlmoxarifado->getCampo("cod_localizacao")) {
                   $stJs.="f.inCodLocalizacao.options[".$i."].selected = true;";
               }

             $i+= 1;
            $rsAlmoxarifado->Proximo();
           }
           SistemaLegado::executaFrameOculto($stJs);
          } else {
           SistemaLegado::exibeAviso(urlencode("Esse almoxarifado não possui Localização. Código : ". $inCodAlmoxarifado),"n_incluir","alerta");
          }
     } else {
       $stJs = "limpaSelect(f.inCodLocalizacao,1);                       \n";
       $stJs.= "f.inCodLocalizacao.options[0] = new Option('Selecione','');";
       SistemaLegado::executaFrameOculto($stJs);
     }
    break;

    //Carrega dados do Arquivo FMManterLocalizacao.php
    case "FMontaLocalizacao" :
        $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
        if ($inCodAlmoxarifado) {
            $obRegraAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
            $obRegraAlmoxarifado->setCodigo( $inCodAlmoxarifado );
            $obRegraAlmoxarifado->consultar();

            $obTxtCodLocalizacao = new TextBox;
            $obTxtCodLocalizacao->setRotulo    ( "Localização"            );
            $obTxtCodLocalizacao->setTitle     ( "Informe a localização." );
            $obTxtCodLocalizacao->setName      ( "stLocalizacao"          );
            $obTxtCodLocalizacao->setId        ( "stLocalizacao"          );
            $obTxtCodLocalizacao->setValue     ( $_REQUEST['HdnLocalizacao'] );
            $obTxtCodLocalizacao->setSize      ( 30                       );
            $obTxtCodLocalizacao->setMaxLength ( 30                       );
            $obTxtCodLocalizacao->setInteiro   ( false                    );
            $obTxtCodLocalizacao->setNull      ( false                    );
            $obTxtCodLocalizacao->obEvento->setOnBlur("VerificaLocalizacao(this,this.value,'');goOculto('ValidaLocalizacao',false);");

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtCodLocalizacao );

            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stValida = $obFormulario->obJavaScript->getInnerJavaScript();

            $stJs  = "d.getElementById('spnListaLocalizacao').innerHTML = '" . $stHtml . "';";
            $stJs .= "f.stEval.value = '" . $stValida . "';                                 ";

            if ($_REQUEST['HdnLocalizacao'] != "") {
                $obRAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao;
                $obRAlmoxarifadoLocalizacao->setCodigo( $inCodLocalizacao );
                $obRAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->setCodigo( $inCodAlmoxarifado );
                $obRAlmoxarifadoLocalizacao->consultar();
                $inCodigo = $obRAlmoxarifadoLocalizacao->getCodigo();
                $inCount = sizeof(Sessao::read('arValores'));

                for ($inPos = 0; $inPos < count($obRAlmoxarifadoLocalizacao->arLocalizacaoItem); $inPos++) {
                    $arValores[$inCount]['id'      ] = ($inCount + 1);
                    $arValores[$inCount]['CodItem' ] = $obRAlmoxarifadoLocalizacao->arLocalizacaoItem[$inPos]->obRCatalogoItem->getCodigo();
                    $arValores[$inCount]['item'    ] = $obRAlmoxarifadoLocalizacao->arLocalizacaoItem[$inPos]->obRCatalogoItem->getDescricao();
                    $arValores[$inCount]['unidade' ] = $obRAlmoxarifadoLocalizacao->arLocalizacaoItem[$inPos]->obRCatalogoItem->obRUnidadeMedida->getNome();
                    $arValores[$inCount]['CodMarca'] = $obRAlmoxarifadoLocalizacao->arLocalizacaoItem[$inPos]->obRMarca->getCodigo();
                    $arValores[$inCount]['marca'   ] = $obRAlmoxarifadoLocalizacao->arLocalizacaoItem[$inPos]->obRMarca->getDescricao();

                    $inCount++;
                }

                Sessao::write('arValores', $arValores);

                $stJs .=  montaListaDotacoes( $arValores );
            }
        } else {
            $stJs .= " d.getElementById('spnListaLocalizacao').innerHTML = '';";
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "LMontaLocalizacao" :
     //Carrega dados do Arquivo LSManterLocalizacao.php
     $obFormulario            = new Formulario();
     $rsAlmoxarifado          = new Recordset;

     $obRegraAlmoxarifado     = new RAlmoxarifadoAlmoxarifado;

     $obRegraAlmoxarifado->setCodigo($inCodAlmoxarifado);
     $obRegraAlmoxarifado->consultar();


     $obTxtObservacao = new TextBox;
     $obTxtObservacao->setRotulo   ('Localização'         );
     $obTxtObservacao->setName     ('stLocalizacao'       );
     $obTxtObservacao->setValue    ($stObservacao         );
     $obTxtObservacao->setSize     ( 34                   );
     $obTxtObservacao->setMaxLength( 160                  );
     $obTxtObservacao->setTitle    ('Informe a localização');

     $obCmbObservacao = new TipoBusca( $obTxtObservacao );
     $obCmbObservacao->obCmbTipoBusca->setValue('contem');

     $obFormulario->addComponente($obCmbObservacao);

     $obFormulario->montaInnerHTML();
     $obFormulario->obJavaScript->montaJavaScript();

     $stValida = $obFormulario->obJavaScript->getInnerJavaScript();

     $stHtml = $obFormulario->getHTML();
     $stJs  .= "d.getElementById('spnListaLocalizacao').innerHTML = '" . $stHtml . "';";
     $stJs  .= "f.stEval.value = '" . $stValida . "';";

     SistemaLegado::executaFrameOculto($stJs);
    break;

    case "IncluirItem":
        $stErro = "";
        //Validar este ítem na lista da localização atual
        foreach (Sessao::read('arValores') as $arTEMP) {
            if ($arTEMP['item'] == $_POST["HdnNomItem"] && $arTEMP['marca'] == $_POST["HdnNomMarca"]) {
                $stErro = "Não pode haver mais de um item da mesma marca nesta localização.";
                break;
            }
        }

        //Validar este ítem em outros almoxarifados
        if ($stErro == "") {
            $obRegra->addLocalizacaoItem();
            $obRegra->roLocalizacaoItem->obRCatalogoItem->setCodigo( $_POST["inCodItem"] );
            $obRegra->roLocalizacaoItem->obRMarca->setCodigo( $_POST["inCodMarca"] );
            $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($_REQUEST['inCodAlmoxarifado']);
            $obRegra->listarItens( $rsItens );
            if ( $rsItens->getNumLinhas() > 0 ) {
                $stErro = "Este item já está cadastrado em outra localização.";
            }

            if ($_REQUEST['stLocalizacao'] == '' and $_REQUEST['HdnLocalizacao'] == '') {
                $stErro = "Informe a localização.";
            }
        }

        if ($stErro == "") {
            include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoItem.class.php" );
            include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoMarca.class.php" );
            $rsListarItens = new recordset();
            $rsListar      = new recordset();

            $obRegra->roLocalizacaoItem->obRCatalogoItem->consultar();
            $obRegra->roLocalizacaoItem->obRMarca->consultar();

            $arValores = Sessao::read('arValores');

            $inCount = sizeof( $arValores );
            $arValores[$inCount]['id'       ] = $inCount + 1;
            $arValores[$inCount]['CodItem'  ] = $_POST["inCodItem"    ];
            $arValores[$inCount]['item'     ] = $obRegra->roLocalizacaoItem->obRCatalogoItem->getDescricao();
            $arValores[$inCount]['unidade'  ] = $obRegra->roLocalizacaoItem->obRCatalogoItem->obRUnidadeMedida->getNome();
            $arValores[$inCount]['CodMarca' ] = $_POST["inCodMarca"   ];
            $arValores[$inCount]['marca'    ] = $obRegra->roLocalizacaoItem->obRMarca->getDescricao();

            Sessao::write('arValores', $arValores);

            $stHTML = montaListaDotacoes( Sessao::read('arValores') );

            $stJs.= "d.getElementById('stLocalizacao').readOnly    = true;";
            $stJs.= "f.inCodItem.value = '';                                   ";
            $stJs.= "d.getElementById('stNomItem').innerHTML       = '&nbsp;'; ";
            $stJs.= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;'; ";
            $stJs.= "f.inCodMarca.value                            = '';       ";
            $stJs.= "d.getElementById('stNomMarca').innerHTML      = '&nbsp;'; ";

        } else {
            $stJs = "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."');\n";
        }
        if ($stJs) {
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;

    case 'excluirItemLocalidade':

       $boDotacaoRepetida = false;
       $arTEMP            = array();
       $inCount           = 0;

        $arValores = Sessao::read('arValores');

       foreach ($arValores as $key => $value) {
          if (($key+1) != $_REQUEST['id']) {
             $arTEMP[$inCount]['id'        ] = $inCount + 1;
             $arTEMP[$inCount]['CodItem'   ] = $value["CodItem" ];
             $arTEMP[$inCount]['item'      ] = $value["item"    ];
             $arTEMP[$inCount]['unidade'   ] = $value["unidade" ];
             $arTEMP[$inCount]['CodMarca'  ] = $value["CodMarca"];
             $arTEMP[$inCount]['marca'     ] = $value["marca"   ];
             $inCount++;
          }
       }

       Sessao::write('arValores' , $arTEMP);

       montaListaDotacoes($arTEMP);
       if (sizeof($arTEMP) == 0) {
          $stJs.= "d.getElementById('stLocalizacao').readOnly    = false;";
          SistemaLegado::executaFrameOculto($stJs);
       }

    break;

    case 'LimpaTela':
        Sessao::write('arValores', array());
    break;

    case 'ValidaLocalizacao':

        include_once TALM."TAlmoxarifadoLocalizacaoFisica.class.php";
        $obTlocalizacao = new TAlmoxarifadoLocalizacaoFisica();
        $stFiltro = " WHERE cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado'];
        $obTlocalizacao->recuperaTodos( $rsLocalizacao , $stFiltro);
        $boLocalizacao = false;

        while (!$rsLocalizacao->eof()) {
            if ( trim($rsLocalizacao->getCampo('localizacao')) == trim($_REQUEST['stLocalizacao']) ) {
                $boLocalizacao = true;
                break;
            }
            $rsLocalizacao->proximo();
        }

        if ($boLocalizacao == true) {
            SistemaLegado::exibeAviso(urlencode("Localização (".$_REQUEST['stLocalizacao'].") já está cadastrada, para incluir mais itens selecione a opção \"Alterar Localização Física\"."),"aviso","alerta");
            $stJs = "f.stLocalizacao.value = ''; \n";
            $stJs .= "f.stLocalizacao.focus();   \n";
        }
        
        SistemaLegado::executaFrameOculto($stJs);
    break;

    }
}

function montaListaDotacoes($arRecordSet , $boExecuta = true)
{
  $rsDotacoes = new RecordSet;
  $rsDotacoes->preenche( $arRecordSet );

  $rsDotacoes->addFormatacao("item","HTML");
  $rsDotacoes->addFormatacao("marca","HTML");

  $obLista = new Lista;

  $obLista->setTitulo('');
  $obLista->setMostraPaginacao( false );
  $obLista->setRecordSet( $rsDotacoes );
  $obLista->addCabecalho();

  $obLista->ultimoCabecalho->addConteudo("&nbsp;");
  $obLista->ultimoCabecalho->setWidth( 5 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Item");
  $obLista->ultimoCabecalho->setWidth( 50 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Unidade de Medida");
  $obLista->ultimoCabecalho->setWidth( 30 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Marca");
  $obLista->ultimoCabecalho->setWidth( 10 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("&nbsp;");
  $obLista->ultimoCabecalho->setWidth( 5 );
  $obLista->commitCabecalho();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "[CodItem] - [item]" );
  $obLista->ultimoDado->setTitle( "item." );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "[unidade]" );
  $obLista->ultimoDado->setTitle( "[unidade]." );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "[CodMarca] - [marca]" );
  $obLista->ultimoDado->setTitle( "marca." );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addAcao();
  $obLista->ultimaAcao->setAcao( "EXCLUIR" );
  $obLista->ultimaAcao->setFuncao( true );
  $obLista->ultimaAcao->setLink( "JavaScript:excluirItemLocalidade();" );
  $obLista->ultimaAcao->addCampo("1","id");
  $obLista->commitAcao();

  $obLista->montaHTML();
  $stHTML = $obLista->getHTML();
  $stHTML = str_replace( "\n" ,"" ,$stHTML );
  $stHTML = str_replace( "  " ,"" ,$stHTML );
  $stHTML = str_replace( "'","\\'",$stHTML );

  if ($boExecuta) {
   SistemaLegado::executaFrameOculto("d.getElementById('spnListaValores').innerHTML = '".$stHTML."';");
  } else {
   return $stHTML;
  }
 }
?>
