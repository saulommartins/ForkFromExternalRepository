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
/*
    * Página Oculto para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * $Id: OCManterPublicacaoContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.23
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include padrão do framework
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"                                 );

$stPrograma = "ManterPublicacaoContrato";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arValores = Sessao::read('arValores');

switch ($_REQUEST['stCtrl']) {

 //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
 case 'carregaListaVeiculos' :
    if ($_REQUEST['inContrato'] != '' AND $_REQUEST['inCodEntidade']) {
        $obTLicitacaoContrato = new TLicitacaoContrato();

          $stFiltro = "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
          $stFiltro.= "   AND contrato.num_contrato = ".$_REQUEST['inContrato']."    \n";
          $stFiltro.= "   AND contrato.exercicio    = ".Sessao::getExercicio()."         \n";

          $obTLicitacaoContrato->recuperaContrato($rsContrato,$stFiltro);
// 		$obTLicitacaoContrato->recuperaPorChave( $rsContrato );
// 		$obTLicitacaoContrato->debug();
        if ( $rsContrato->getNumLinhas() > 0 ) {
            include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoContrato.class.php"                       );
            $obTLicitacaoPublicacaoContrato = new TLicitacaoPublicacaoContrato();
            $obTLicitacaoPublicacaoContrato->setDado('num_contrato',$_REQUEST['inContrato']);
            $obTLicitacaoPublicacaoContrato->setDado('exercicio',Sessao::getExercicio());
            $obTLicitacaoPublicacaoContrato->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTLicitacaoPublicacaoContrato->recuperaVeiculosPublicacao( $rsPublicacaoContrato );
            $inCount = 0 ;
            while ( !$rsPublicacaoContrato->eof() ) {
                $arValores[$inCount]['id'               ] = $inCount + 1;
                $arValores[$inCount]['inVeiculo'        ] = $rsPublicacaoContrato->getCampo('num_veiculo');
                $arValores[$inCount]['stVeiculo'        ] = $rsPublicacaoContrato->getCampo('nom_veiculo');
                $arValores[$inCount]['dtDataVigencia'   ] = $rsPublicacaoContrato->getCampo('dt_publicacao');
                $arValores[$inCount]['stObservacao'     ] = $rsPublicacaoContrato->getCampo('observacao');
                $arValores[$inCount]['inCodLicitacao'   ] = $rsPublicacaoContrato->getCampo('cod_licitacao');
                $inCount++;
                $rsPublicacaoContrato->proximo();
            }
            $codLicitacao = $rsContrato->getCampo("cod_licitacao");
               $codObjeto    = $rsContrato->getCampo("cod_objeto");
               $stObjeto     = nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsContrato->getCampo("descricao"))));
               $codModalidade= $rsContrato->getCampo("cod_modalidade");

            $js = "d.getElementById('inNroLicitacao').innerHTML = '".$codLicitacao."';             ";
               $js.= "d.getElementById('inNroObjeto')   .innerHTML = '".$codObjeto." - ".$stObjeto."';";

               $js.= "f.HdnCodContrato.value  = '".$_REQUEST['inContrato']."';                        ";
               $js.= "f.HdnCodLicitacao.value = '".$codLicitacao."';                                  ";
               $js.= "f.HdnCodModalidade.value= '".$codModalidade."';                                 ";
        } else {
            $js = "f.inContrato.value = '';";
            $js.= "alertaAviso('Número do contrato inválido!','form','erro','".Sessao::getId()."');";
            $js = "d.getElementById('inNroLicitacao').innerHTML = '&nbsp;'; ";
               $js.= "d.getElementById('inNroObjeto')   .innerHTML = '&nbsp;'; ";
               $js.= "f.HdnCodContrato.value  = '';                      ";
               $js.= "f.HdnCodLicitacao.value = '';                      ";
               $js.= "f.HdnCodModalidade.value= '';                      ";
            $arValores = array();
        }
    } else {
        $js = "f.inContrato.value = '';";
        $js.= "alertaAviso('Número do contrato inválido!','form','erro','".Sessao::getId()."');";
        $js = "d.getElementById('inNroLicitacao').innerHTML = '&nbsp;'; ";
        $js.= "d.getElementById('inNroObjeto')   .innerHTML = '&nbsp;'; ";
        $js.= "f.HdnCodContrato.value  = '';                      ";
        $js.= "f.HdnCodLicitacao.value = '';                      ";
        $js.= "f.HdnCodModalidade.value= '';                      ";
        $arValores = array();
    }
   echo $js;
   echo montaListaVeiculos($arValores);
   Sessao::write('arValores', $arValores);
 break;

 //Inclui itens na listagem de veiculos de publicacao utilizados
 case 'incluirListaVeiculos':

 $boPublicacaoRepetida = false;
  foreach ($arValores as $arTEMP) {
     if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataVigencia'] == $_REQUEST['dtDataVigencia']) {
         $boPublicacaoRepetida = true ;
         break;
     }
  }
  if (!($boPublicacaoRepetida)) {
     $inCount = sizeof($arValores);
     $arValores[$inCount]['id'               ] = $inCount + 1;
     $arValores[$inCount]['inVeiculo'        ] = $_REQUEST[ "inVeiculo"                  ];
     $arValores[$inCount]['stVeiculo'        ] = $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
     $arValores[$inCount]['dtDataVigencia'   ] = $_REQUEST[ "dtDataVigencia"             ];
     $arValores[$inCount]['stObservacao'     ] = $_REQUEST[ "stObservacao"               ];
     $arValores[$inCount]['inCodLicitacao'   ] = $_REQUEST[ "HdnCodLicitacao"            ];
  } else {
     echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
  }
    echo montaListaVeiculos( $arValores);
    Sessao::write('arValores', $arValores);
 break;

 //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
 case 'alterarListaVeiculos':
  $i = 0;
  foreach ($arValores as $key => $value) {
   if (($key+1) == $_REQUEST['id']) {
    $js ="f.HdnCodVeiculo.value                      ='".$_REQUEST['id']."';                                            ";
    $js.="f.inVeiculo.value                          ='".$arValores[$i]['inVeiculo']."';             ";
    $js.="f.dtDataVigencia.value                     ='".$arValores[$i]['dtDataVigencia']."';        ";
    $js.="f.stObservacao.value                       ='".$arValores[$i]['stObservacao']."';          ";
    $js.="d.getElementById('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';";
    $js.="d.getElementById('incluiVeiculo').value    ='Alterar';                                                        ";
    $js.="f.stCtrl.value                             ='alteradoListaVeiculos';                                          ";
    }
   $i++;
 }
 echo $js;
 Sessao::write('arValores', $arValores);
 break;

 //Confirma itens alterados da listagem de veiculos de publicacao utilizados
 case "alteradoListaVeiculos":
  $inCount = 0;
  $boDotacaoRepetida = false;
  foreach ($arValores as $arTEMP) {
     if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataVigencia'] == $_REQUEST['dtDataVigencia']) {
         $boDotacaoRepetida = true ;
         break;
     }
  }
  if (!$boDotacaoRepetida) {
        foreach ($arValores as $key=>$value) {
         if (($key+1) == $_REQUEST['HdnCodVeiculo']) {
           $arValores[$inCount]['id'            ] = $inCount + 1;
           $arValores[$inCount]['inVeiculo'     ] = $_REQUEST[ "inVeiculo"                  ];
           $arValores[$inCount]['stVeiculo'     ] = $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
           $arValores[$inCount]['dtDataVigencia'] = $_REQUEST[ "dtDataVigencia"             ];
           $arValores[$inCount]['stObservacao'  ] = $_REQUEST[ "stObservacao"               ];
         }
          $inCount++;
        }

        $js ="d.getElementById('incluiVeiculo').value = 'Incluir';";
        echo $js.montaListaVeiculos($arValores);
        Sessao::write('arValores', $arValores);
 } else {
    echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
 }
 break;

 //Exclui itens da listagem de veiculos de publicacao utilizados
 case 'excluirListaVeiculos':

  $boDotacaoRepetida = false;
  $arTEMP            = array();
  $inCount           = 0;

  foreach ($arValores as $key => $value) {
   if (($key+1) != $_REQUEST['id']) {
     $arTEMP[$inCount]['id'               ] = $inCount + 1;
     $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"      ];
     $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"      ];
     $arTEMP[$inCount]['dtDataVigencia'   ] = $value[ "dtDataVigencia" ];
     $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"   ];
     $arTEMP[$inCount]['inCodLicitacao'   ] = $value[ "inCodLicitacao" ];
    $inCount++;
  }
    if (sizeOf($arValores)<=1) {
       $js = "f.inCodEntidade.disabled = false;";
       $js.= "f.stNomEntidade.disabled = false;";
       $js.= "f.inContrato.disabled    = false;";
       echo $js;
    }
 }
  $arValores = $arTEMP;
  echo montaListaVeiculos($arValores);
  Sessao::write('arValores', $arValores);
 break;

 //Consulta Temporária enquanto o componente IPopUpNumeroContrato não fica pronto.
 case 'consultaContrato':
  if ($_REQUEST['inContrato']!="") {
    if ($_REQUEST['inCodEntidade']!="") {
      $rsRecordSetVeiculo  = new RecordSet;
      $obLicitacaoContrato = new TLicitacaoContrato();

      $stFiltro = "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
      $stFiltro.= "   AND contrato.num_contrato = ".$_REQUEST['inContrato']."    \n";
      $stFiltro.= "   AND contrato.exercicio    = ".Sessao::getExercicio()."         \n";

      $obLicitacaoContrato->recuperaContrato($rsRecordSetVeiculo,$stFiltro);
      if (!($rsRecordSetVeiculo->EOF())) {
        while (!($rsRecordSetVeiculo->EOF())) {
           $codLicitacao = $rsRecordSetVeiculo->getCampo("cod_licitacao");
           $codObjeto    = $rsRecordSetVeiculo->getCampo("cod_objeto");
           $stObjeto     = nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSetVeiculo->getCampo("descricao"))));
           $codModalidade= $rsRecordSetVeiculo->getCampo("cod_modalidade");

           $js = "d.getElementById('inNroLicitacao').innerHTML = '".$codLicitacao."';             ";
           $js.= "d.getElementById('inNroObjeto')   .innerHTML = '".$codObjeto." - ".$stObjeto."';";

           $js.= "f.HdnCodContrato.value  = '".$_REQUEST['inContrato']."';                        ";
           $js.= "f.HdnCodLicitacao.value = '".$codLicitacao."';                                  ";
           $js.= "f.HdnCodModalidade.value= '".$codModalidade."';                                 ";

         $rsRecordSetVeiculo->proximo();
        }
      } else {
        $js = "f.inContrato.value      = '';                                                      ";

        $js.= "f.HdnCodContrato.value   = '';                                                     ";
        $js.= "f.HdnCodLicitacao.value  = '';                                                     ";
        $js.= "f.HdnCodModalidade.value = '';                                                     ";

        $js.="alertaAviso('Número do Contrato(".$_GET['inContrato'].") não encontrado!.','form','erro','".Sessao::getId()."');";
      }
    } else {
       $js ="f.inContrato.value      = '';                                          ";

       $js.="f.HdnCodContrato.value  = '';                                          ";
       $js.="f.HdnCodLicitacao.value = '';                                          ";
       $js.="f.HdnCodModalidade.value= '';                                          ";

       $js.="alertaAviso('Selecione uma entidade.','form','erro','".Sessao::getId()."');";
    }
  }
 echo $js;
 break;
}

 function montaListaVeiculos($arRecordSet , $boExecuta = true)
 {
  $stPrograma = "ManterContrato";
  $pgOcul     = "OC".$stPrograma.".php";

  $rsVeiculos = new RecordSet;
  $rsVeiculos->preenche( $arRecordSet );

  $obLista = new Lista;

  $obLista->setTitulo('');
  $obLista->setMostraPaginacao( false );
  $obLista->setRecordSet( $rsVeiculos );
  $obLista->addCabecalho();

  $obLista->ultimoCabecalho->addConteudo("&nbsp;");
  $obLista->ultimoCabecalho->setWidth( 5 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Veículos de Publicação");
  $obLista->ultimoCabecalho->setWidth( 35 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Data");
  $obLista->ultimoCabecalho->setWidth( 15 );
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Observação");
  $obLista->ultimoCabecalho->setWidth( 25 );
  $obLista->commitCabecalho();

  $obLista->ultimoCabecalho->addConteudo("Ação");
  $obLista->ultimoCabecalho->setWidth( 5 );
  $obLista->commitCabecalho();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "[inVeiculo]-[stVeiculo]" );
  $obLista->ultimoDado->setTitle( "veículo." );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "dtDataVigencia" );
  $obLista->ultimoDado->setTitle( "data" );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addDado();
  $obLista->ultimoDado->setCampo( "stObservacao" );
  $obLista->ultimoDado->setTitle( "observação." );
  $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
  $obLista->commitDado();

  $obLista->addAcao();
  $obLista->ultimaAcao->setAcao( "ALTERAR" );
  $obLista->ultimaAcao->setFuncao( true );
  $obLista->ultimaAcao->setLink( "JavaScript:alterarListaVeiculos();" );
  $obLista->ultimaAcao->addCampo("1","id");
  $obLista->commitAcao();

  $obLista->addAcao();
  $obLista->ultimaAcao->setAcao( "EXCLUIR" );
  $obLista->ultimaAcao->setFuncao( true );
  $obLista->ultimaAcao->setLink( "JavaScript:excluirListaVeiculos();" );
  $obLista->ultimaAcao->addCampo("1","id");
  $obLista->commitAcao();

  $obLista->montaHTML();
  $stHTML = $obLista->getHTML();
  $stHTML = str_replace( "\n" ,"" ,$stHTML );
  $stHTML = str_replace( "  " ,"" ,$stHTML );
  $stHTML = str_replace( "'","\\'",$stHTML );

  if ($boExecuta) {
   return "d.getElementById('spnListaVeiculos').innerHTML = '".$stHTML."';";
  } else {
   return $stHTML;
  }

 }
