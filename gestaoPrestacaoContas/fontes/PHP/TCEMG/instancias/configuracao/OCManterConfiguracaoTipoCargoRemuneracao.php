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
  * Página de Oculto de Configuração dos Tipos de Cargos Remuneracao
  * Data de Criação: 16/03/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Evandro Melos
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoRequisitosCargo.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoRemuneracao.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoCargoServidor.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalCargo.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalSubDivisao.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoCargoRemuneracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

function montaListaRegime($arRegimeSubDivisao)
{
    $obTTCEMGTipoCargoServidor = new TTCEMGTipoCargoServidor();
    
    $i = 0;
    foreach ($arRegimeSubDivisao as $key => $value) {        
        $obTTCEMGTipoCargoServidor->recuperaTodos($rsTipoCargoServidor," WHERE cod_tipo = ".$key." "," ORDER BY cod_tipo",$boTransacao);
        $arTipoCargoServidor[$i]['cod_tipo']  = $rsTipoCargoServidor->getCampo('cod_tipo');
        $arTipoCargoServidor[$i]['descricao'] = $rsTipoCargoServidor->getCampo('descricao');
        $i++;
    }

    $rsTipoCargoServidor = new RecordSet();
    $rsTipoCargoServidor->preenche($arTipoCargoServidor);
    $rsTipoCargoServidor->setPrimeiroElemento();

    $obTableTree = new TableTree;
    $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoCargoRemuneracao.php' );
    $obTableTree->setParametros  ( array("cod_tipo") );
    $obTableTree->setComplementoParametros( "stCtrl=detalharRegimeSubDivisao" );
    $obTableTree->setRecordset   ( $rsTipoCargoServidor );
    $obTableTree->setSummary     ( 'Lista de Configurações de Tipos de Cargos' );
    $obTableTree->setConditional ( true );
    $obTableTree->Head->AddCabecalho( 'Sigla/Tipo de Cargo',30 );    
    $obTableTree->Body->setStyle ( 'font-weight: bold;' );
    $obTableTree->Body->addCampo ( '[descricao]', 'E' );
    
    $obTableTree->Body->addAcao  ( 'alterar','executaFuncaoAjax(\'%s\',\'&inCodTipoCargo=%s\')',array('alterarRegimeSubDivisao','cod_tipo') );
    $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inCodTipoCargo=%s\')',array('excluirRegimeSubDivisao','cod_tipo') );
    $obTableTree->montaHTML      ( true );
    $stHTML = $obTableTree->getHtml();

    $stJs .= " jq('#spnListaRegimeSubDivisao').html('".$stHTML."'); \n";

    return $stJs;
}
function montaListaRequisitoCargo($arRequisitosCargos)
{
    $obTTCEMGTipoRequisitosCargo = new TTCEMGTipoRequisitosCargo();
    
    $i = 0;
    foreach ($arRequisitosCargos as $key => $value) {        
        $obTTCEMGTipoRequisitosCargo->recuperaTodos($rsTipoCargoServidor," WHERE cod_tipo = ".$key." "," ORDER BY cod_tipo",$boTransacao);
        $arAux[$i]['cod_tipo']  = $rsTipoCargoServidor->getCampo('cod_tipo');
        $arAux[$i]['descricao'] = $rsTipoCargoServidor->getCampo('descricao');
        $i++;
    }

    $rsTipoRequisitoCargo = new RecordSet();
    $rsTipoRequisitoCargo->preenche($arAux);
    $rsTipoRequisitoCargo->setPrimeiroElemento();

    $obTableTree = new TableTree;
    $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoCargoRemuneracao.php' );
    $obTableTree->setParametros  ( array("cod_tipo") );
    $obTableTree->setComplementoParametros( "stCtrl=detalharRequisitosCargo" );
    $obTableTree->setRecordset   ( $rsTipoRequisitoCargo );
    $obTableTree->setSummary     ( 'Lista de Configurações de Requisitos dos Cargos' );    
    $obTableTree->Head->AddCabecalho( 'Codigo/Tipo de Requisito',30 );    
    $obTableTree->Body->setStyle ( 'font-weight: bold;' );
    $obTableTree->Body->addCampo ( '[cod_tipo] - [descricao]', 'E' );
    
    $obTableTree->Body->addAcao  ( 'alterar','executaFuncaoAjax(\'%s\',\'&inCodTipoRequisitoCargo=%s\')',array('alterarRequisitosCargo','cod_tipo') );
    $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inCodTipoRequisitoCargo=%s\')',array('excluirRequisitosCargo','cod_tipo') );
    $obTableTree->montaHTML      ( true );
    $stHTML = $obTableTree->getHtml();

    $stJs .= " jq('#spnListaRequisitosCargos').html('".$stHTML."'); \n";

    return $stJs;   
}

function montaListaEventos($arEventos)
{
    $obTTCEMGTipoRemuneracao = new TTCEMGTipoRemuneracao();

    $i = 0;
    foreach ($arEventos as $key => $value) {
        $obTTCEMGTipoRemuneracao->recuperaTodos($rsTipoRemuneracao," WHERE cod_tipo = ".$key." ",'',$boTransacao);
        $arAux[$i]['cod_tipo']  = $rsTipoRemuneracao->getCampo('cod_tipo');
        $arAux[$i]['descricao'] = $rsTipoRemuneracao->getCampo('descricao');
        $i++;
    }

    $rsEventos = new RecordSet();
    $rsEventos->preenche($arAux);
    $rsEventos->setPrimeiroElemento();

    $obTableTree = new TableTree;
    $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoCargoRemuneracao.php' );
    $obTableTree->setParametros  ( array("cod_tipo") );
    $obTableTree->setComplementoParametros( "stCtrl=detalharRemuneracaoEventos" );
    $obTableTree->setRecordset   ( $rsEventos );
    $obTableTree->setSummary     ( 'Lista de Tipos de Remuneração' );    
    $obTableTree->Head->AddCabecalho( 'Codigo/Tipo Remuneracao',30 );
    $obTableTree->Body->setStyle ( 'font-weight: bold;' );    
    $obTableTree->Body->addCampo ( '[cod_tipo] - [descricao]', 'E' );
    
    $obTableTree->Body->addAcao  ( 'alterar','executaFuncaoAjax(\'%s\',\'&inCodRemuneracao=%s\')',array('alterarRemuneracao','cod_tipo') );
    $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inCodRemuneracao=%s\')',array('excluirRemuneracao','cod_tipo') );
    $obTableTree->montaHTML      ( true );
    $stHTML = $obTableTree->getHtml();

    $stJs .= " jq('#spnListaEventos').html('".$stHTML."'); \n";

    return $stJs;   

}

switch ($stCtrl) {
    //----------------------------------------------------
    //Informações do Tipo de Cargo do Servidor 
    //----------------------------------------------------
    case 'incluirRegimeSubDivisao':
        $arRegimeSubDivisao = Sessao::read('arRegimeSubDivisao');
        $arRegimeSubDivisao = empty($arRegimeSubDivisao) ? array() : $arRegimeSubDivisao;
        $stAcaoCampos = Sessao::read('acaoCampos');
        
        if ( ($request->get('cmbTipoCargo') != '') && ($request->get('arRegimeSubdivisaoSelecionados') != '') && ($request->get('arCargosRegimeSelecionados') != '') ) {
            
            if ($stAcaoCampos == 'alterar') {
                if( array_key_exists($request->get('cmbTipoCargo'), $arRegimeSubDivisao) ){                    
                    unset($arRegimeSubDivisao[$request->get('cmbTipoCargo')]);
                }    
            }

            foreach ($arRegimeSubDivisao as $codTipoCargo => $subDivisao) {                
                foreach ($subDivisao as $codSubDivisao => $cargos) {
                    foreach ($cargos as $codCargo => $value) {
                        if ( in_array($codSubDivisao,$request->get('arRegimeSubdivisaoSelecionados')) ) {
                            if ( in_array($codCargo,$request->get('arCargosRegimeSelecionados') ) ) {
                                $stJs = "alertaAviso('Este Regime/Subdivisão e Cargo (".$value.") já está sendo usado em outro Tipo de Cargo!', 'aviso', 'aviso','".Sessao::getId()."');";
                                echo $stJs;
                                exit();
                            }
                        }    
                    }
                }
            }

            foreach ($request->get('arRegimeSubdivisaoSelecionados') as $key => $value) {                    
                foreach ($request->get('arCargosRegimeSelecionados') as $key2 => $value2) {
                    $obTPessoalCargo = new TPessoalCargo ;
                    $obTPessoalCargo->recuperaTodos( $rsPessoalCargo," WHERE cod_cargo = ".$value2." "," ORDER BY cod_cargo",$boTransacao );
                    //array [cod_tipo_regime] [cod_sub_divisao] [cod_cargo] 
                    $arRegimeSubDivisao[$request->get('cmbTipoCargo')][$value][$value2] = $rsPessoalCargo->getCampo('cod_cargo')." - ".$rsPessoalCargo->getCampo('descricao');
                }   
            }
            
            Sessao::write('arRegimeSubDivisao',$arRegimeSubDivisao);
            
            $stJs  = montaListaRegime($arRegimeSubDivisao);
            $stJs .= " JavaScript:passaItem('document.frm.arRegimeSubdivisaoSelecionados','document.frm.arRegimeSubdivisaoDisponiveis','tudo','valueText'); ";
            $stJs .= " JavaScript:passaItem('document.frm.arCargosRegimeSelecionados','document.frm.arCargosRegimeDisponiveis','tudo','valueText'); ";
            $stJs .= " jq('#cmbTipoCargo').val(''); ";
            $stJs .= " jq('#btnIncluirRegimeSubDivisao').val('Incluir'); ";
            $stJs .= " jq('#cmbTipoCargo').prop( 'disabled', false ); ";
            Sessao::write('acaoCampos','configurar');
        
        }else{
            $stJs = "alertaAviso('Selecione todos os campos das Informações do Tipo de Cargo do Servidor', 'aviso', 'aviso','".Sessao::getId()."');";
        }
    
    break;

    case 'detalharRegimeSubDivisao':
        $arAux = Sessao::read('arRegimeSubDivisao');
        //Buscando quais os regimes foram selecionados no campo **Regime/Subdivisão   
        $obTPessoalSubDivisao = new TPessoalSubDivisao();
        $arRegimeSubDivisao = $arAux[$request->get('cod_tipo')];//cmbTipoCargo        
        foreach ($arRegimeSubDivisao as $chave => $regime) {
            $obTPessoalSubDivisao->recuperaRelacionamento($rsRegimeSubDivisao," AND psd.cod_sub_divisao = ".$chave." ",'',$boTransacao);
            //Nome do regime
            $arRegimes[]['nom_regime'] = $rsRegimeSubDivisao->getCampo('nom_regime')." - ".$rsRegimeSubDivisao->getCampo('nom_sub_divisao');            
            foreach ($regime as $value) {                
                $arRegimes[]['nom_cargo'] = $value;
            }
        }
        
        $rsRegimes = new RecordSet();
        $rsRegimes->preenche($arRegimes);
        $rsRegimes->setPrimeiroElemento();
        
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRegimes );
        $obLista->setTitulo ("Cargos");

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Regime");
        $obLista->ultimoCabecalho->setWidth( 18 );                
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Cargos");
        $obLista->ultimoCabecalho->setAlign( "direita" );
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "CSS" );
        $obLista->ultimoDado->setCampo( "nom_regime" );
        $obLista->ultimoDado->setClass("show_dados_center_bold");

        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->ultimoDado->setCampo( "nom_cargo" );
        $obLista->commitDado();
        
        $obLista->montaHTML();
        
        $stJs .= $obLista->getHTML();

    break;

    case 'alterarRegimeSubDivisao':        
        $arAux = Sessao::read('arRegimeSubDivisao');
        $arRegimeSubDivisao = $arAux[$request->get('inCodTipoCargo')];
        
        foreach ($arRegimeSubDivisao as $codRegime => $cargos) {
            $stJs .= " jq('#arRegimeSubdivisaoDisponiveis').val('".$codRegime."').dblclick(); ";
            $arCargosRegime = $arRegimeSubDivisao[$codRegime];
        }
        foreach ($arCargosRegime as $codCargos => $valueCargos) {                
                $stJs .= " jq('#arCargosRegimeDisponiveis').val('".$codCargos."').dblclick(); ";
        }
        
        $stJs .= " jq('#btnIncluirRegimeSubDivisao').val('Alterar');";
        $stJs .= " jq('#cmbTipoCargo').val('".$request->get('inCodTipoCargo')."'); ";
        $stJs .= " jq('#cmbTipoCargo').prop( 'disabled', true ); ";
        
        Sessao::write('acaoCampos','alterar');
        
    break;

    case 'excluirRegimeSubDivisao':
        $arAux = Sessao::read('arRegimeSubDivisao');
        unset($arAux[$request->get('inCodTipoCargo')]);
        
        Sessao::write('arRegimeSubDivisao', $arAux);
        $stJs = montaListaRegime($arAux);
        $stJs .= " jq('#arRegimeSubdivisaoDisponiveis').focus(''); ";

    break;
    
    case 'limparListaRegimeSubDivisao':
        $stJs  = " JavaScript:passaItem('document.frm.arRegimeSubdivisaoSelecionados','document.frm.arRegimeSubdivisaoDisponiveis','tudo','valueText'); ";
        $stJs .= " JavaScript:passaItem('document.frm.arCargosRegimeSelecionados','document.frm.arCargosRegimeDisponiveis','tudo','valueText'); ";
        $stJs .= " jq('#cmbTipoCargo').val(''); ";
        $stJs .= " jq('#cmbTipoCargo').prop( 'disabled', false ); ";
        $stJs .= " jq('#btnIncluirRegimeSubDivisao').val('Incluir'); ";
    break;

    //----------------------------------------------------
    //Informações de Requisitos dos Cargos
    //----------------------------------------------------
    case 'incluirRequisitosCargos':
        $arRequisitosCargos = Sessao::read('arRequisitosCargos');
        $arRequisitosCargos = empty($arRequisitosCargos) ? array() : $arRequisitosCargos;
        $stAcaoCampos = Sessao::read('acaoCampos');

        if ( ($request->get('cmbTipoRequisitosCargos') != '') && ($request->get('arRequisitosCargosSelecionados') != '')  ) {
            
            if ($stAcaoCampos == 'alterar') {
                if( array_key_exists($request->get('cmbTipoRequisitosCargos'), $arRequisitosCargos) ){
                    unset($arRequisitosCargos[$request->get('cmbTipoRequisitosCargos')]);
                }
            }

            foreach ($arRequisitosCargos as $codTipoCargo => $cargos) {
                foreach ($cargos as $codCargo => $value) {
                    if ( in_array($codCargo,$request->get('arRequisitosCargosSelecionados') ) ) {
                        $stJs = "alertaAviso('Este Cargo (".$value.") já está sendo usado em outro Requisito do Cargo!', 'aviso', 'aviso','".Sessao::getId()."');";
                        echo $stJs;
                        exit();
                    }
                }
            }

            foreach ($request->get('arRequisitosCargosSelecionados') as $key => $value) {                                    
                $obTPessoalCargo = new TPessoalCargo ;
                $obTPessoalCargo->recuperaTodos( $rsPessoalCargo," WHERE cod_cargo = ".$value." "," ORDER BY cod_cargo",$boTransacao );
                //array [cod_tipo_requisito_cargo] [cod_cargo]
                $arRequisitosCargos[$request->get('cmbTipoRequisitosCargos')][$value] = $rsPessoalCargo->getCampo('cod_cargo')." - ".$rsPessoalCargo->getCampo('descricao');
            }
            
            Sessao::write('arRequisitosCargos',$arRequisitosCargos);
                        
            $stJs  = montaListaRequisitoCargo($arRequisitosCargos);
            $stJs .= " JavaScript:passaItem('document.frm.arRequisitosCargosSelecionados','document.frm.arRequisitosCargosDisponivel','tudo','valueText'); ";            
            $stJs .= " jq('#cmbTipoRequisitosCargos').val(''); ";
            $stJs .= " jq('#btnIncluirRequisitosCargos').val('Incluir'); ";
            $stJs .= " jq('#cmbTipoRequisitosCargos').prop( 'disabled', false ); ";            
            Sessao::write('acaoCampos','configurar');
                    
        }else{
            $stJs = "alertaAviso('Selecione todos os campos das  Informações de Requisitos dos Cargos', 'aviso', 'aviso','".Sessao::getId()."');";
        }

    break;

    case 'detalharRequisitosCargo':
        $arAux = Sessao::read('arRequisitosCargos');
        $arAux = $arAux[$request->get('cod_tipo')];
        
        $obTPessoalCargo = new TPessoalCargo;
        
        foreach ($arAux as $chave => $cargos) {
            //Nome do cargo
            $obTPessoalCargo->recuperaTodos( $rsPessoalCargo," WHERE cod_cargo = ".$chave." "," ORDER BY cod_cargo",$boTransacao );            
            $arRequesitosCargo[]['nom_cargo'] = $rsPessoalCargo->getCampo('cod_cargo')." - ".$rsPessoalCargo->getCampo('descricao');
        }
        
        $rsRegimes = new RecordSet();
        $rsRegimes->preenche($arRequesitosCargo);
        $rsRegimes->setPrimeiroElemento();
        
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRegimes );
        $obLista->setTitulo ("Cargos");

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Cargos");
        $obLista->ultimoCabecalho->setAlign( "direita" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->ultimoDado->setCampo( "nom_cargo" );
        $obLista->commitDado();
        
        $obLista->montaHTML();
        
        $stJs .= $obLista->getHTML();

    break;

    case 'alterarRequisitosCargo':
        $arAux = Sessao::read('arRequisitosCargos');
        $arRequisitosCargos = $arAux[$request->get('inCodTipoRequisitoCargo')];
        
        $stJs .= " jq('#arRequisitosCargosSelecionados').empty(); ";
        
        foreach ($arRequisitosCargos as $key => $value) {
            $stJs .= " jq('#arRequisitosCargosDisponivel').val('".$key."').dblclick(); ";
        }
        
        $stJs .= " jq('#btnIncluirRequisitosCargos').val('Alterar');";
        $stJs .= " jq('#cmbTipoRequisitosCargos').val('".$request->get('inCodTipoRequisitoCargo')."'); ";
        $stJs .= " jq('#arRequisitosCargosSelecionados').focus(); ";
        $stJs .= " jq('#cmbTipoRequisitosCargos').prop( 'disabled', true ); ";
        Sessao::write('acaoCampos','alterar');
    break;

    case 'excluirRequisitosCargo':
        $arAux = Sessao::read('arRequisitosCargos');
        unset($arAux[$request->get('inCodTipoRequisitoCargo')]);
        
        Sessao::write('arRequisitosCargos', $arAux);
        $stJs = montaListaRequisitoCargo($arAux);
        $stJs .= " jq('#arRequisitosCargosDisponivel').focus(); ";
    break;
    
    case 'limparRequisitosCargos':
        $stJs  = " JavaScript:passaItem('document.frm.arRequisitosCargosSelecionados','document.frm.arRequisitosCargosDisponivel','tudo','valueText'); ";        
        $stJs .= " jq('#cmbTipoRequisitosCargos').val(''); ";
        $stJs .= " jq('#arRequisitosCargosDisponivel').focus(); ";
        $stJs .= " jq('#btnIncluirRequisitosCargos').val('Incluir'); ";
        $stJs .= " jq('#cmbTipoRequisitosCargos').prop( 'disabled', false ); ";            
    break;

    //----------------------------------------------------
    //Informações de Tipos de Remuneração
    //----------------------------------------------------
    case 'incluirEventos':
        $arEventos = Sessao::read('arEventos');        
        $arEventos = empty($arEventos) ? array() : $arEventos;
        $stAcaoCampos = Sessao::read('acaoCampos');

        if ( ($request->get('cmbTipoRemuneracao') != '') && ($request->get('arEventosSelecionados') != '')  ) {
            
            if ($stAcaoCampos == 'alterar') {
                if( array_key_exists($request->get('cmbTipoRemuneracao'), $arEventos) ){
                    unset($arEventos[$request->get('cmbTipoRemuneracao')]);
                }
            }

            foreach ($arEventos as $codTipoCargo => $evento) {
                foreach ($evento as $codEvento => $value) {
                    if ( in_array($codEvento,$request->get('arEventosSelecionados') ) ) {
                        $stJs = "alertaAviso('Este Evento (".$value.") já está sendo usado em outro Tipo de Remuneração!', 'aviso', 'aviso','".Sessao::getId()."');";
                        echo $stJs;
                        exit();
                    }
                }
            }

            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            foreach ($request->get('arEventosSelecionados') as $key => $value) {                                    
                $obTFolhaPagamentoEvento->recuperaTodos($rsEventos," WHERE cod_evento = ".$value." ",'',$boTransacao);
                //array [cod_tipo_remuneracao] [codigo]
                $arEventos[$request->get('cmbTipoRemuneracao')][$value] = $rsEventos->getCampo('codigo')." - ".$rsEventos->getCampo('descricao');
            }
            
            Sessao::write('arEventos',$arEventos);
                        
            $stJs  = montaListaEventos($arEventos);
            $stJs .= " JavaScript:passaItem('document.frm.arEventosSelecionados','document.frm.arEventosDisponiveis','tudo','valueText'); ";
            $stJs .= " jq('#cmbTipoRemuneracao').val(''); ";
            $stJs .= " jq('#btnIncluirEventos').val('Incluir'); ";            
            $stJs .= " jq('#cmbTipoRemuneracao').prop( 'disabled', false ); ";            
            Sessao::write('acaoCampos','configurar');
                    
        }else{
            $stJs = "alertaAviso('Selecione todos os campos das Informações de Tipos de Remuneração', 'aviso', 'aviso','".Sessao::getId()."');";
        }

    break;

    case 'detalharRemuneracaoEventos':
        $arAux = Sessao::read('arEventos');
        $arAux = $arAux[$request->get('cod_tipo')];
        
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();

        foreach ($arAux as $chave => $eventos) {
            //Nome do evento
            $obTFolhaPagamentoEvento->recuperaTodos( $rsEventos," WHERE cod_evento = ".$chave." "," ORDER BY cod_evento",$boTransacao );            
            $arEventos[]['nom_evento'] = $rsEventos->getCampo('codigo')." - ".$rsEventos->getCampo('descricao');
        }
        
        $rsEventos = new RecordSet();
        $rsEventos->preenche($arEventos);
        $rsEventos->setPrimeiroElemento();
        
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsEventos );
        $obLista->setTitulo ("Eventos");

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Eventos");
        $obLista->ultimoCabecalho->setAlign( "direita" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->ultimoDado->setCampo( "nom_evento" );
        $obLista->commitDado();
        
        $obLista->montaHTML();
        
        $stJs .= $obLista->getHTML();

    break;

    case 'alterarRemuneracao':
        
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        
        $arAux = Sessao::read('arEventos');
        $arEventos = $arAux[$request->get('inCodRemuneracao')];
        
        $stJs .= " jq('#arEventosSelecionados').empty(); ";
        
        foreach ($arEventos as $key => $value) {
            $stJs .= " jq('#arEventosDisponiveis').val('".$key."').dblclick(); ";
        }
        
        $stJs .= " jq('#btnIncluirEventos').val('Alterar');";
        $stJs .= " jq('#cmbTipoRemuneracao').val('".$request->get('inCodRemuneracao')."'); ";
        $stJs .= " jq('#arEventosSelecionados').focus(); ";
        $stJs .= " jq('#cmbTipoRemuneracao').prop( 'disabled', true ); ";
        Sessao::write('acaoCampos','alterar');
        
    break;

    case 'excluirRemuneracao':
        $arAux = Sessao::read('arEventos');
        unset($arAux[$request->get('inCodRemuneracao')]);
        
        Sessao::write('arEventos', $arAux);
        $stJs = montaListaEventos($arAux);
        $stJs .= " jq('#arEventosDisponiveis').focus(); ";

    break;
    
    case 'limparEventos':
        $stJs  = " JavaScript:passaItem('document.frm.arEventosSelecionados','document.frm.arEventosDisponiveis','tudo','valueText'); ";        
        $stJs .= " jq('#cmbTipoRemuneracao').val(''); ";
        $stJs .= " jq('#arEventosDisponiveis').focus(); ";
    break;

    case 'carregaDados':
        //----------------------------------------------------
        //Informações do Tipo de Cargo do Servidor 
        //----------------------------------------------------
        include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeCargoServidor.class.php';
        $obTFolhaPagamentoTCEMGEntidadeCargoServidor = new TFolhaPagamentoTCEMGEntidadeCargoServidor();
        $stFiltro = " WHERE tcemg_entidade_cargo_servidor.exercicio = '".Sessao::getExercicio()."' ";
        $obTFolhaPagamentoTCEMGEntidadeCargoServidor->recuperaDadosConfiguracao($rsCargoServidor,$stFiltro,"",$boTransacao);
        if( $rsCargoServidor->getNumLinhas() > 0 ) {
            foreach ($rsCargoServidor->getElementos() as $key => $value) {
                $arRegimeSubDivisao[$value['cod_tipo']] [$value['cod_sub_divisao']][$value['cod_cargo'] ] = $value['nom_cargo'];
            }    
        }
        if (is_array($arRegimeSubDivisao)) {
            Sessao::write('arRegimeSubDivisao',$arRegimeSubDivisao);
            $stJs .= montaListaRegime($arRegimeSubDivisao);
        }

        //----------------------------------------------------
        //Informações de Requisitos dos Cargos
        //----------------------------------------------------
        include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeRequisitosCargo.class.php';
        $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo = new TFolhaPagamentoTCEMGEntidadeRequisitosCargo();
        $stFiltro = " WHERE tcemg_entidade_requisitos_cargo.exercicio = '".Sessao::getExercicio()."' ";
        $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->recuperaDadosConfiguracao($rsRequisitoCargo,$stFiltro,"",$boTransacao);
        if( $rsRequisitoCargo->getNumLinhas() > 0 ) {
            foreach ($rsRequisitoCargo->getElementos() as $key => $value) {                
                $arRequisitosCargos[$value['cod_tipo']][$value['cod_cargo']] = $value['nom_cargo'];
            }    
        }
        if (is_array($arRequisitosCargos)) {
            Sessao::write('arRequisitosCargos',$arRequisitosCargos);
            $stJs .= montaListaRequisitoCargo($arRequisitosCargos);
        }
        
        //----------------------------------------------------
        //Informações de Tipos de Remuneração
        //----------------------------------------------------
        include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeRemuneracao.class.php';
        $obTFolhaPagamentoTCEMGEntidadeRemuneracao = new TFolhaPagamentoTCEMGEntidadeRemuneracao();
        $stFiltro = " WHERE tcemg_entidade_remuneracao.exercicio = '".Sessao::getExercicio()."' ";
        $obTFolhaPagamentoTCEMGEntidadeRemuneracao->recuperaDadosConfiguracao($rsRemuneracao,$stFiltro,"",$boTransacao);
        if( $rsRemuneracao->getNumLinhas() > 0 ) {
            foreach ($rsRemuneracao->getElementos() as $key => $value) {                
                $arEventos[$value['cod_tipo']][$value['cod_evento']] = $value['nom_evento'];
            }    
        }
        if (is_array($arEventos)) {
            Sessao::write('arEventos',$arEventos);
            $stJs .= montaListaEventos($arEventos);
        }

        $stJs .= " LiberaFrames(true,true); ";
    break;

    case 'limparTudo':
        $stJs  = " JavaScript:passaItem('document.frm.arRegimeSubdivisaoSelecionados','document.frm.arRegimeSubdivisaoDisponiveis','tudo','valueText'); ";
        $stJs .= " JavaScript:passaItem('document.frm.arCargosRegimeSelecionados','document.frm.arCargosRegimeDisponiveis','tudo','valueText'); ";
        $stJs .= " JavaScript:passaItem('document.frm.arRequisitosCargosSelecionados','document.frm.arRequisitosCargosDisponivel','tudo','valueText'); ";        
        $stJs .= " JavaScript:passaItem('document.frm.arEventosSelecionados','document.frm.arEventosDisponiveis','tudo','valueText'); ";        
        $stJs .= " jq('#cmbTipoCargo').val(''); ";
    break;

}//END OF SWITCH

if ($stJs != '') {
    echo $stJs;
}


?>