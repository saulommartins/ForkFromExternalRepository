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
  * Página de Oculto de Configuração de Tipos de Salários
  * Data de Criação: 27/10/2015

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
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
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoFuncaoServidor.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBACargoServidor.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoFuncaoServidorTemporario.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBACargoServidorTemporario.class.php';
include_once CAM_GT_MON_MAPEAMENTO.'TMONBanco.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAEmprestimoConsignado.class.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioBase.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAVantagensSalariais.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAGratificacaoFuncao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioFamilia.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioHorasExtras.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioDescontos.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAPlanoSaude.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoServidor.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoLotacao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoLocal.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoSalario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

switch ($stCtrl) {

    /*** Tipo Função Servidor ***/
    case "incluirFuncaoServidorLista":
        unset($inCounter);

        $cmbTipofuncaoServidor        = $request->get('cmbTipofuncaoServidor');
        $arCargosSelecionadosServidor = $request->get('arCargosSelecionadosServidor');

        if ($cmbTipofuncaoServidor != '' && $arCargosSelecionadosServidor != '') {
            $arFuncaoCargoServidorSessao = Sessao::read('arFuncaoCargoServidor');

            if (is_array($arFuncaoCargoServidorSessao)) {
                foreach ($arFuncaoCargoServidorSessao as $arFuncaoCargoServidorTmp) {
                    if ($arFuncaoCargoServidorTmp['cod_tipo_funcao'] == $cmbTipofuncaoServidor) {
                        echo "alertaAviso('@Esta Função de Servidor já está cadastrada na Lista de Funções(s).','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            $obTTCMBATipoFuncaoServidor = new TTCMBATipoFuncaoServidor();
            $obTTCMBATipoFuncaoServidor->setDado('cod_tipo_funcao', $cmbTipofuncaoServidor);
            $obTTCMBATipoFuncaoServidor->recuperaPorChave($rsTipoFuncaoServidor);

            $obTPessoalCargo = new TPessoalCargo();
            $obTPessoalCargo->recuperaTodos( $rsPessoalCargo,  ' WHERE cod_cargo IN ('.implode(',', $arCargosSelecionadosServidor).')' );

            $inCounter = (Sessao::read('arFuncaoCargoServidor') == "") ? 0 : count(Sessao::read('arFuncaoCargoServidor'));

            $arFuncaoCargoServidorSessao[$inCounter]['id']              = $inCounter;
            $arFuncaoCargoServidorSessao[$inCounter]['cod_tipo']        = $inCounter;
            $arFuncaoCargoServidorSessao[$inCounter]['cod_tipo_funcao'] = $rsTipoFuncaoServidor->getCampo('cod_tipo_funcao');
            $arFuncaoCargoServidorSessao[$inCounter]['descricao']       = $rsTipoFuncaoServidor->getCampo('descricao');
            $arFuncaoCargoServidorSessao[$inCounter]['cargos']          = $rsPessoalCargo->arElementos;
            
            Sessao::write('arFuncaoCargoServidor',$arFuncaoCargoServidorSessao);

            $stJs  =  montaListaFuncaoServidor("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.arCargosSelecionadosServidor','document.frm.arCargosDisponiveisServidor','tudo');";
            $stJs .= "jq('select#cmbTipofuncaoServidor').selectOptions('');";
            echo "alertaAviso('Função e cargo(s) inseridos na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione uma função e pelo menos um cargo.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirFuncaoServidorLista":
        $inCount = 0;
        
        if (is_array(Sessao::read('arFuncaoCargoServidor'))) {
            foreach (Sessao::read('arFuncaoCargoServidor') as $arFuncaoCargoServidorTmp ) {
                if ($arFuncaoCargoServidorTmp["cod_tipo"] != $request->get("inVlTipo")) {
                    $arTmp[$inCount] = $arFuncaoCargoServidorTmp;
                    $inCount++;
                }
            }
        }

        echo "alertaAviso('Tipo de Função excluida.','','info','".Sessao::getId()."');";
        echo " jq('#arCargosDisponiveisServidor').focus(); ";
        
        Sessao::write('arFuncaoCargoServidor',$arTmp);
        $stJs = montaListaFuncaoServidor("mostrar");
        echo $stJs;
    break;

    case "detalharListaCargoServidor":
        $inCodTipo = $request->get('cod_tipo');

        $arFuncaoCargoServidor = Sessao::read('arFuncaoCargoServidor');
        for ($i = 0 ;$i < count(Sessao::read('arFuncaoCargoServidor')); $i++) {
            if ($inCodTipo == $arFuncaoCargoServidor[$i]["cod_tipo"]) {
                $rsFuncaoCargos = new RecordSet ;
                $rsFuncaoCargos->preenche($arFuncaoCargoServidor[$i]["cargos"]);
                break;
            }
        }

        while (!$rsFuncaoCargos->EOF()) {
            $rsFuncaoCargos->setCampo('cod_tipo', $inCodTipo);
            $rsFuncaoCargos->proximo();
        }

        $obTable = new Table;
        $obTable->setRecordset( $rsFuncaoCargos );
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Cargos', 50);
        $obTable->Body->addCampo('[cod_cargo] - [descricao]', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoTipoSalario.php?cod_cargo=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirCargo&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_cargo', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;

    case "excluirCargo":
        $arFuncaoCargoServidorSessao = Sessao::read('arFuncaoCargoServidor');

        foreach ($arFuncaoCargoServidorSessao AS $arFuncaoCargoServidorTmp) {
            if ($arFuncaoCargoServidorTmp['cod_tipo'] == $request->get('cod_tipo')) {

                if (count($arFuncaoCargoServidorTmp['cargos']) == 1) {
                    echo "alertaAviso('@Não é possível deletar este cargo, pois ele é o único relacionado a esta função.','form','erro','".Sessao::getId()."');";
                    echo " jq('#arCargosDisponiveisServidor').focus(); ";
                    die;
                }

                foreach ($arFuncaoCargoServidorTmp['cargos'] AS $arCargoTmp) {
                    if ($arCargoTmp['cod_cargo'] != $request->get('cod_cargo')) {
                        $arOcorrenciaNova[] = $arCargoTmp;
                    }
                }
                $arFuncaoCargoServidorTmp['cargos'] = $arOcorrenciaNova;
            }
            $arCargosNovasSessao[] = $arFuncaoCargoServidorTmp;
        }

        echo "alertaAviso('Cargo excluido.','','info','".Sessao::getId()."');";
        echo " jq('#arCargosDisponiveisServidor').focus(); ";

        Sessao::write('arFuncaoCargoServidor', $arCargosNovasSessao);
        $stJs = montaListaFuncaoServidor("mostrar");
        echo $stJs;
    break;

    case "limparListaCargoServidor":
            $stJs .= "JavaScript:passaItem('document.frm.arCargosSelecionadosServidor','document.frm.arCargosDisponiveisServidor','tudo');";
            $stJs .= "jq('select#cmbTipofuncaoServidor').selectOptions('');";
        echo  $stJs;
    break;

    case "funcoesExistentes":
        $obTTCMBATipoFuncaoServidor = new TTCMBATipoFuncaoServidor();
        $obTTCMBATipoFuncaoServidor->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBATipoFuncaoServidor->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBATipoFuncaoServidor->recuperaFuncaoCargo($rsFuncaoCargo);

        $inCounter = 0;
        $arFuncaoCargoServidorSessao = array();
        
        foreach ($rsFuncaoCargo->arElementos as $tipoFuncao) {
            $obTTCMBACargoServidor = new TTCMBACargoServidor();
            $obTTCMBACargoServidor->setDado('cod_tipo_funcao', $tipoFuncao['cod_tipo_funcao']);
            $obTTCMBACargoServidor->setDado('cod_entidade'   , Sessao::read('cod_entidade'));
            $obTTCMBACargoServidor->setDado('exercicio'      , Sessao::getExercicio());
            $obTTCMBACargoServidor->recuperaCargos($rsCargos , ' ORDER BY cod_cargo ');
            
            $arFuncaoCargoServidorSessao[$inCounter]['id']              = $inCounter;
            $arFuncaoCargoServidorSessao[$inCounter]['cod_tipo']        = $inCounter;
            $arFuncaoCargoServidorSessao[$inCounter]['cod_tipo_funcao'] = $tipoFuncao['cod_tipo_funcao'];
            $arFuncaoCargoServidorSessao[$inCounter]['descricao']       = $tipoFuncao['descricao'];
            $arFuncaoCargoServidorSessao[$inCounter]['cargos']          = $rsCargos->arElementos;

            $inCounter++;
        }

        Sessao::write('arFuncaoCargoServidor', $arFuncaoCargoServidorSessao);

        $stJs = montaListaFuncaoServidor("mostrar");
        echo $stJs;
    break;
    
    /*** Tipo Função Servidor Temporário ***/
    case "incluirFuncaoServidorListaTemporario":
        unset($inCounter);

        $cmbTipofuncaoServidorTemporario        = $request->get('cmbTipofuncaoServidorTemporario');
        $arCargosSelecionadosServidorTemporario = $request->get('arCargosSelecionadosServidorTemporario');

        if ($cmbTipofuncaoServidorTemporario != '' && $arCargosSelecionadosServidorTemporario != '') {
            $arFuncaoCargoServidorSessaoTemporario = Sessao::read('arFuncaoCargoServidorTemporario');

            if (is_array($arFuncaoCargoServidorSessaoTemporario)) {
                foreach ($arFuncaoCargoServidorSessaoTemporario as $arFuncaoCargoServidorTmpTemporario) {
                    if ($arFuncaoCargoServidorTmpTemporario['cod_tipo_funcao'] == $cmbTipofuncaoServidorTemporario) {
                        echo "alertaAviso('@Esta Função de Servidor Temporário já está cadastrada na Lista de Funções(s).','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            $obTTCMBATipoFuncaoServidorTemporario = new TTCMBATipoFuncaoServidorTemporario();
            $obTTCMBATipoFuncaoServidorTemporario->setDado('cod_tipo_funcao', $cmbTipofuncaoServidorTemporario);
            $obTTCMBATipoFuncaoServidorTemporario->recuperaPorChave($rsTipoFuncaoServidorTemporario);

            $obTPessoalCargo = new TPessoalCargo();
            $obTPessoalCargo->recuperaTodos( $rsPessoalCargo,  ' WHERE cod_cargo IN ('.implode(',', $arCargosSelecionadosServidorTemporario).')' );
     
            $inCounter = (Sessao::read('arFuncaoCargoServidorTemporario') == "") ? 0 : count(Sessao::read('arFuncaoCargoServidorTemporario'));
            
            $arFuncaoCargoServidorSessaoTemporario[$inCounter]['id']              = $inCounter;
            $arFuncaoCargoServidorSessaoTemporario[$inCounter]['cod_tipo']        = $inCounter;
            $arFuncaoCargoServidorSessaoTemporario[$inCounter]['cod_tipo_funcao'] = $rsTipoFuncaoServidorTemporario->getCampo('cod_tipo_funcao');
            $arFuncaoCargoServidorSessaoTemporario[$inCounter]['descricao']       = $rsTipoFuncaoServidorTemporario->getCampo('descricao');
            $arFuncaoCargoServidorSessaoTemporario[$inCounter]['cargos']          = $rsPessoalCargo->arElementos;

            Sessao::write('arFuncaoCargoServidorTemporario',$arFuncaoCargoServidorSessaoTemporario);
            
            
            $stJs  =  montaListaFuncaoServidorTemporario("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.arCargosSelecionadosServidorTemporario','document.frm.arCargosDisponiveisServidorTemporario','tudo');";
            $stJs .= "jq('select#cmbTipofuncaoServidorTemporario').selectOptions('');";
            echo "alertaAviso('Função e cargo(s) inseridos na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione uma função e pelo menos um cargo.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirFuncaoServidorListaTemporario":
        $inCount = 0;
        
        if (is_array(Sessao::read('arFuncaoCargoServidorTemporario'))) {
            foreach (Sessao::read('arFuncaoCargoServidorTemporario') as $arFuncaoCargoServidorTmpTemporario ) {
                if ($arFuncaoCargoServidorTmpTemporario["cod_tipo"] != $request->get("inVlTipo")) {
                    $arTmp[$inCount] = $arFuncaoCargoServidorTmpTemporario;
                    $inCount++;
                }
            }
        }

        echo "alertaAviso('Função de Servidor Temporário excluida.','','info','".Sessao::getId()."');";
        echo " jq('#arCargosDisponiveisServidorTemporario').focus(); ";

        Sessao::write('arFuncaoCargoServidorTemporario',$arTmp);
        $stJs = montaListaFuncaoServidorTemporario("mostrar");
        echo $stJs;
    break;

    case "detalharListaCargoServidorTemporario":
        $inCodTipo = $request->get('cod_tipo');

        $arFuncaoCargoServidorTemporario = Sessao::read('arFuncaoCargoServidorTemporario');
        for ($i = 0 ;$i < count(Sessao::read('arFuncaoCargoServidorTemporario')); $i++) {
            if ($inCodTipo == $arFuncaoCargoServidorTemporario[$i]["cod_tipo"]) {
                $rsFuncaoCargosTemporario = new RecordSet ;
                $rsFuncaoCargosTemporario->preenche($arFuncaoCargoServidorTemporario[$i]["cargos"]);
                break;
            }
        }

        while (!$rsFuncaoCargosTemporario->EOF()) {
            $rsFuncaoCargosTemporario->setCampo('cod_tipo', $inCodTipo);
            $rsFuncaoCargosTemporario->proximo();
        }

        $obTable = new Table;
        $obTable->setRecordset( $rsFuncaoCargosTemporario );
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Cargos', 50);
        $obTable->Body->addCampo('[cod_cargo] - [descricao]', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoTipoSalario.php?cod_cargo=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirCargoTemporario&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_cargo', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;

    case "excluirCargoTemporario":
        $arFuncaoCargoServidorSessaoTemporario = Sessao::read('arFuncaoCargoServidorTemporario');

        foreach ($arFuncaoCargoServidorSessaoTemporario AS $arFuncaoCargoServidorTmpTempoario) {
            if ($arFuncaoCargoServidorTmpTempoario['cod_tipo'] == $request->get('cod_tipo')) {

                if (count($arFuncaoCargoServidorTmpTempoario['cargos']) == 1) {
                    echo "alertaAviso('@Não é possível deletar este cargo, pois ele é o único relacionado a esta função.','form','erro','".Sessao::getId()."');";
                    echo " jq('#arCargosDisponiveisServidorTemporario').focus(); ";
                    die;
                }

                foreach ($arFuncaoCargoServidorTmpTempoario['cargos'] AS $arCargoTmp) {
                    if ($arCargoTmp['cod_cargo'] != $request->get('cod_cargo')) {
                        $arCargoNovo[] = $arCargoTmp;
                    }
                }
                $arFuncaoCargoServidorTmpTempoario['cargos'] = $arCargoNovo;
            }
            $arCargosNovasSessao[] = $arFuncaoCargoServidorTmpTempoario;
        }

        echo "alertaAviso('Cargo excluido.','','info','".Sessao::getId()."');";
        echo " jq('#arCargosDisponiveisServidorTemporario').focus(); ";

        Sessao::write('arFuncaoCargoServidorTemporario', $arCargosNovasSessao);
        $stJs = montaListaFuncaoServidorTemporario("mostrar");
        echo $stJs;
    break;

    case "limparListaCargoServidorTemporario":
            $stJs .= "JavaScript:passaItem('document.frm.arCargosSelecionadosServidorTemporario','document.frm.arCargosDisponiveisServidorTemporario','tudo');";
            $stJs .= "jq('select#cmbTipofuncaoServidorTemporario').selectOptions('');";
        echo  $stJs;
    break;

    case "funcoesTemporarioExistentes":
        $obTTCMBATipoFuncaoServidorTemporario = new TTCMBATipoFuncaoServidorTemporario();
        $obTTCMBATipoFuncaoServidorTemporario->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBATipoFuncaoServidorTemporario->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBATipoFuncaoServidorTemporario->recuperaFuncaoCargoTemporario($rsFuncaoCargoTemporario);

        $inCounter = 0;
        $arFuncaoCargoServidorTemporarioSessao = array();
        
        foreach ($rsFuncaoCargoTemporario->arElementos as $tipoFuncao) {
            $obTTCMBACargoServidorTemporario = new TTCMBACargoServidorTemporario();
            $obTTCMBACargoServidorTemporario->setDado('cod_tipo_funcao', $tipoFuncao['cod_tipo_funcao']);
            $obTTCMBACargoServidorTemporario->setDado('cod_entidade'  , Sessao::read('cod_entidade'));
            $obTTCMBACargoServidorTemporario->setDado('exercicio'     , Sessao::getExercicio());
            $obTTCMBACargoServidorTemporario->recuperaCargosTemporario($rsCargos, ' ORDER BY cod_cargo ');

            $arFuncaoCargoServidorTemporarioSessao[$inCounter]['id']              = $inCounter;
            $arFuncaoCargoServidorTemporarioSessao[$inCounter]['cod_tipo']        = $inCounter;
            $arFuncaoCargoServidorTemporarioSessao[$inCounter]['cod_tipo_funcao'] = $tipoFuncao['cod_tipo_funcao'];
            $arFuncaoCargoServidorTemporarioSessao[$inCounter]['descricao']       = $tipoFuncao['descricao'];
            $arFuncaoCargoServidorTemporarioSessao[$inCounter]['cargos']  = $rsCargos->arElementos;

            $inCounter++;
        }

        Sessao::write('arFuncaoCargoServidorTemporario', $arFuncaoCargoServidorTemporarioSessao);

        $stJs = montaListaFuncaoServidorTemporario("mostrar");
        echo $stJs;
    break;
    
    /*** Bancos Emprestimos ***/
    case "incluirBancoEmprestimo":
        unset($inCounter);

        $cmbBancoEmprestimo    = $request->get('cmbBancoEmprestimo');
        $arEventosSelecionados = $request->get('arEventosSelecionados');
        
        if ($cmbBancoEmprestimo != '' && $arEventosSelecionados != '') {
            $arBancoEventosEmprestimoSessao = Sessao::read('arBancoEventosEmprestimo');

            if (is_array($arBancoEventosEmprestimoSessao)) {
                foreach ($arBancoEventosEmprestimoSessao as $arBancoEventosEmprestimoTmp) {
                    if ($arBancoEventosEmprestimoTmp['cod_banco'] == $cmbBancoEmprestimo) {
                        echo "alertaAviso('@Esta Código de compensação do banco já está cadastrado na Lista.','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            $obTMONBanco = new TMONBanco();
            $obTMONBanco->setDado('cod_banco', $cmbBancoEmprestimo);
            $obTMONBanco->recuperaPorChave($rsBancoEmprestimo);
            
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $obTFolhaPagamentoEvento->recuperaTodos( $rsEventosEmprestimo, ' WHERE cod_evento IN ('.implode(',', $arEventosSelecionados).')' );

            $inCounter = (Sessao::read('arBancoEventosEmprestimo') == "") ? 0 : count(Sessao::read('arBancoEventosEmprestimo'));
            
            $arBancoEventosEmprestimoSessao[$inCounter]['id']        = $inCounter;
            $arBancoEventosEmprestimoSessao[$inCounter]['cod_tipo']  = $inCounter;
            $arBancoEventosEmprestimoSessao[$inCounter]['cod_banco'] = $rsBancoEmprestimo->getCampo('cod_banco');
            $arBancoEventosEmprestimoSessao[$inCounter]['num_banco'] = $rsBancoEmprestimo->getCampo('num_banco');
            $arBancoEventosEmprestimoSessao[$inCounter]['nom_banco'] = $rsBancoEmprestimo->getCampo('nom_banco');
            $arBancoEventosEmprestimoSessao[$inCounter]['eventos']    = $rsEventosEmprestimo->arElementos;

            Sessao::write('arBancoEventosEmprestimo',$arBancoEventosEmprestimoSessao);
            
            $stJs  =  montaListaBancoEmprestimo("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.arEventosSelecionados','document.frm.arEventosDisponiveis','tudo');";
            $stJs .= "jq('select#cmbBancoEmprestimo').selectOptions('');";
            echo "alertaAviso('Banco e evento(s) inseridos na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione um banco e pelo menos um evento.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirBancoEmprestimoLista":
        $inCount = 0;
        
        if (is_array(Sessao::read('arBancoEventosEmprestimo'))) {
            foreach (Sessao::read('arBancoEventosEmprestimo') as $arBancoEventosEmprestimoTmp ) {
                if ($arBancoEventosEmprestimoTmp["cod_tipo"] != $request->get("inVlTipo")) {
                    $arTmp[$inCount] = $arBancoEventosEmprestimoTmp;
                    $inCount++;
                }
            }
        }

        echo "alertaAviso('Código de compensação do banco do Empréstimo excluido.','','info','".Sessao::getId()."');";
        echo "jq('#arEventosDisponiveis').focus();";
        
        Sessao::write('arBancoEventosEmprestimo',$arTmp);
        $stJs = montaListaBancoEmprestimo("mostrar");
        echo $stJs;
    break;

    case "detalharListaEventosEmprestimo":
        $inCodTipo = $request->get('cod_tipo');

        $arBancoEventosEmprestimo = Sessao::read('arBancoEventosEmprestimo');
        for ($i = 0 ;$i < count(Sessao::read('arBancoEventosEmprestimo')); $i++) {
            if ($inCodTipo == $arBancoEventosEmprestimo[$i]["cod_tipo"]) {
                $rsBancoEventosEmprestimo = new RecordSet ;
                $rsBancoEventosEmprestimo->preenche($arBancoEventosEmprestimo[$i]["eventos"]);
                break;
            }
        }

        while (!$rsBancoEventosEmprestimo->EOF()) {
            $rsBancoEventosEmprestimo->setCampo('cod_tipo', $inCodTipo);
            $rsBancoEventosEmprestimo->proximo();
        }
        $obTable = new Table;
        $obTable->setRecordset( $rsBancoEventosEmprestimo );
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Empréstimos Consignados', 50);
        $obTable->Body->addCampo('[codigo] - [descricao]', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoTipoSalario.php?cod_evento=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirEventosEmprestimo&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_evento', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;

    case "excluirEventosEmprestimo":
        $arBancoEventosEmprestimoSessao = Sessao::read('arBancoEventosEmprestimo');

        foreach ($arBancoEventosEmprestimoSessao AS $arBancoEventosEmprestimoTmp) {
            if ($arBancoEventosEmprestimoTmp['cod_tipo'] == $request->get('cod_tipo')) {

                if (count($arBancoEventosEmprestimoTmp['eventos']) == 1) {
                    echo "alertaAviso('@Não é possível deletar este evento de empréstimo, pois ele é o único relacionado a esta código de compensação.','form','erro','".Sessao::getId()."');";
                    echo "jq('#arEventosDisponiveis').focus();";
                    die;
                }

                foreach ($arBancoEventosEmprestimoTmp['eventos'] AS $arEventosTmp) {
                    if ($arEventosTmp['cod_evento'] != $request->get('cod_evento')) {
                        $arEventosNovo[] = $arEventosTmp;
                    }
                }
                $arBancoEventosEmprestimoTmp['eventos'] = $arEventosNovo;
            }
            $arEventosNovaSessao[] = $arBancoEventosEmprestimoTmp;
        }

        echo "alertaAviso('Evento de empréstimo excluido.','','info','".Sessao::getId()."');";
        echo "jq('#arEventosDisponiveis').focus();";
        
        Sessao::write('arBancoEventosEmprestimo', $arEventosNovaSessao);
        $stJs = montaListaBancoEmprestimo("mostrar");
        echo $stJs;
    break;

    case "limparListaEventosEmprestimo":
            $stJs .= "JavaScript:passaItem('document.frm.arEventosSelecionados','document.frm.arEventosDisponiveis','tudo');";
            $stJs .= "jq('select#cmbBancoEmprestimo').selectOptions('');";
        echo  $stJs;
    break;

    case "bancoEventosExistentes":
        $obTTCMBAEmprestimoConsignado = new TTCMBAEmprestimoConsignado();
        $obTTCMBAEmprestimoConsignado->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBAEmprestimoConsignado->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBAEmprestimoConsignado->recuperaBancosEmprestimo($rsBancoEventos);

        $inCounter = 0;
        $arBancoEventosEmprestimoSessao = array();

         foreach ($rsBancoEventos->arElementos as $bancoEvento) {
            $obTTCMBAEmprestimoConsignado->setDado('cod_banco', $bancoEvento['cod_banco']);
            $obTTCMBAEmprestimoConsignado->recuperaEventosEmprestimo($rEventos, ' ORDER BY evento.cod_evento ');
            
            $arBancoEventosEmprestimoSessao[$inCounter]['id']        = $inCounter;
            $arBancoEventosEmprestimoSessao[$inCounter]['cod_tipo']  = $inCounter;
            $arBancoEventosEmprestimoSessao[$inCounter]['cod_banco'] = $bancoEvento['cod_banco'];
            $arBancoEventosEmprestimoSessao[$inCounter]['num_banco'] = $bancoEvento['num_banco'];
            $arBancoEventosEmprestimoSessao[$inCounter]['nom_banco'] = $bancoEvento['nom_banco'];
            $arBancoEventosEmprestimoSessao[$inCounter]['eventos']   = $rEventos->arElementos;

            $inCounter++;
        }

        Sessao::write('arBancoEventosEmprestimo', $arBancoEventosEmprestimoSessao);

        $stJs = montaListaBancoEmprestimo("mostrar");
        echo $stJs;
    break;

    /*** Salário Base ***/
    case "salarioBaseExistentes":
   
        $obTTCMBASalarioBase = new TTCMBASalarioBase();
        $obTTCMBASalarioBase->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBASalarioBase->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBASalarioBase->recuperaEventosSalarioBase($rsEventosSalarioBase);
        
        if ( $rsEventosSalarioBase->getNumLinhas() > 0 ) {
            
            while (!$rsEventosSalarioBase->eof()) {
                $stJs .= " jq('#arSalariosBaseDisponiveis').removeOption('" . $rsEventosSalarioBase->getCampo('cod_evento') . "'); ";
                $stJs .= " jq('#arSalarioBaseSelecionados').addOption('" . $rsEventosSalarioBase->getCampo('cod_evento') . "','" . $rsEventosSalarioBase->getCampo('codigo')." - ".$rsEventosSalarioBase->getCampo('descricao') . "');";
                $rsEventosSalarioBase->proximo();
            }
            echo $stJs;    
        }
        
    break;

    /*** Demais Vantagens Salariais ***/
    case "vantagensSalariaisExistentes":
   
        $obTTCMBAVantagensSalariais = new TTCMBAVantagensSalariais();
        $obTTCMBAVantagensSalariais->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBAVantagensSalariais->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBAVantagensSalariais->recuperaEventosVantagensSalariais($rsEventosVantagensSalariais);
        
        if ( $rsEventosVantagensSalariais->getNumLinhas() > 0 ) {
            
            while (!$rsEventosVantagensSalariais->eof()) {
                $stJs .= " jq('#arVantagensSalariaisDisponiveis').removeOption('" . $rsEventosVantagensSalariais->getCampo('cod_evento') . "'); ";
                $stJs .= " jq('#arVantagensSalariaisSelecionados').addOption('" . $rsEventosVantagensSalariais->getCampo('cod_evento') . "','" . $rsEventosVantagensSalariais->getCampo('codigo')." - ".$rsEventosVantagensSalariais->getCampo('descricao') . "');";
                $rsEventosVantagensSalariais->proximo();
            }
            echo $stJs;    
        }
        
    break;
    
    /*** Gratificação de função ***/
    case "gratificacaoFuncaoExistentes":
   
        $obTTCMBAGratificacaoFuncao = new TTCMBAGratificacaoFuncao();
        $obTTCMBAGratificacaoFuncao->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBAGratificacaoFuncao->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBAGratificacaoFuncao->recuperaEventosGratificacaoFuncao($rsEventosGratificacaoFuncao);

        if ( $rsEventosGratificacaoFuncao->getNumLinhas() > 0 ) {
            
            while (!$rsEventosGratificacaoFuncao->eof()) {
                $stJs .= " jq('#arGratificacaoFuncaoDisponiveis').removeOption('" . $rsEventosGratificacaoFuncao->getCampo('cod_evento') . "'); ";
                $stJs .= " jq('#arGratificacaoFuncaoSelecionados').addOption('" . $rsEventosGratificacaoFuncao->getCampo('cod_evento') . "','" . $rsEventosGratificacaoFuncao->getCampo('codigo')." - ".$rsEventosGratificacaoFuncao->getCampo('descricao') . "');";
                $rsEventosGratificacaoFuncao->proximo();
            }
            echo $stJs;    
        }
        
    break;

    /*** Salário Família ***/
    case "salarioFamiliaExistentes":
   
        $oTTCMBASalarioFamilia = new TTCMBASalarioFamilia();
        $oTTCMBASalarioFamilia->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $oTTCMBASalarioFamilia->setDado('exercicio'   , Sessao::getExercicio());
        $oTTCMBASalarioFamilia->recuperaEventosSalarioFamilia($rsEventosSalarioFamilia);
        
        if ( $rsEventosSalarioFamilia->getNumLinhas() > 0 ) {
            
            while (!$rsEventosSalarioFamilia->eof()) {
                $stJs .= " jq('#arSalarioFamiliaDisponiveis').removeOption('" . $rsEventosSalarioFamilia->getCampo('cod_evento') . "'); ";
                $stJs .= " jq('#arSalarioFamiliaSelecionados').addOption('" . $rsEventosSalarioFamilia->getCampo('cod_evento') . "','" . $rsEventosSalarioFamilia->getCampo('codigo')." - ".$rsEventosSalarioFamilia->getCampo('descricao') . "');";
                $rsEventosSalarioFamilia->proximo();
            }
            echo $stJs;    
        }
        
    break;

    /*** Horas Extras trabalhadas ***/
    case "horasExtrasExistentes":
   
        $obTTCMBASalarioHorasExtras = new TTCMBASalarioHorasExtras();
        $obTTCMBASalarioHorasExtras->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBASalarioHorasExtras->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBASalarioHorasExtras->recuperaEventosHorasExtras($rsEventosHorasExtras);
        
        if ( $rsEventosHorasExtras->getNumLinhas() > 0 ) {
            
            while (!$rsEventosHorasExtras->eof()) {
                $stJs .= " jq('#arHorasExtrasDisponiveis').removeOption('" . $rsEventosHorasExtras->getCampo('cod_evento') . "'); ";
                $stJs .= "jq('#arHorasExtrasSelecionados').addOption('" . $rsEventosHorasExtras->getCampo('cod_evento') . "','" . $rsEventosHorasExtras->getCampo('codigo')." - ".$rsEventosHorasExtras->getCampo('descricao') . "');";
                $rsEventosHorasExtras->proximo();
            }
            echo $stJs;    
        }
        
    break;

    /*** Demais Descontos ***/
    case "demaisDescontosExistentes":
   
        $oTTCMBASalarioDescontos = new TTCMBASalarioDescontos();
        $oTTCMBASalarioDescontos->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $oTTCMBASalarioDescontos->setDado('exercicio'   , Sessao::getExercicio());
        $oTTCMBASalarioDescontos->recuperaEventosDemaisDescontos($rsEventosDemaisDescontos);
        
        if ( $rsEventosDemaisDescontos->getNumLinhas() > 0 ) {
            
            while (!$rsEventosDemaisDescontos->eof()) {
                $stJs .= " jq('#arDemaisDescontosDisponiveis').removeOption('" . $rsEventosDemaisDescontos->getCampo('cod_evento') . "'); ";
                $stJs .= " jq('#arDemaisDescontosSelecionados').addOption('" . $rsEventosDemaisDescontos->getCampo('cod_evento') . "','" . $rsEventosDemaisDescontos->getCampo('codigo')." - ".$rsEventosDemaisDescontos->getCampo('descricao') . "');";
                $rsEventosDemaisDescontos->proximo();
            }
            echo $stJs;    
        }
        
    break;

     /*** Plano de Saúde/Odontológico ***/
    case "planoSaudeExistentes":
   
        $oTTCMBAPlanoSaude = new TTCMBAPlanoSaude();
        $oTTCMBAPlanoSaude->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $oTTCMBAPlanoSaude->setDado('exercicio'   , Sessao::getExercicio());
        $oTTCMBAPlanoSaude->recuperaEventosPlanoSaude($rsEventosPlanoSaude);
        
        if ( $rsEventosPlanoSaude->getNumLinhas() > 0 ) {
            
            while (!$rsEventosPlanoSaude->eof()) {
                $stJs .= " jq('#arPlanoSaudeDisponiveis').removeOption('" . $rsEventosPlanoSaude->getCampo('cod_evento') . "'); ";
                $stJs .= "jq('#arPlanoSaudeSelecionados').addOption('" . $rsEventosPlanoSaude->getCampo('cod_evento') . "','" . $rsEventosPlanoSaude->getCampo('codigo')." - ".$rsEventosPlanoSaude->getCampo('descricao') . "');";
                $rsEventosPlanoSaude->proximo();
            }
            echo $stJs;    
        }
        
    break;
    
    /*** Classe/Aplicação do Salário do Servidor ***/
    case "incluirFonteRecursoServidor":
        unset($inCounter);

        $cmbFonteRecursoServidor  = $request->get('cmbFonteRecursoServidor');
        $arCodLotacaoSelecionados = $request->get('inCodLotacaoSelecionados');
        $arCodLocalSelecionados   = $request->get('inCodLocalSelecionados');
        
        if ($cmbFonteRecursoServidor != '' && $arCodLotacaoSelecionados != '') {
            $arFonteRecursoLotacaoLocal = Sessao::read('arFonteRecursoLotacaoLocal');

            if (is_array($arFonteRecursoLotacaoLocal)) {
                foreach ($arFonteRecursoLotacaoLocal as $arFonteRecursoLotacaoLocalTmp) {
                    if ($arFonteRecursoLotacaoLocalTmp['cod_tipo_fonte'] == $cmbFonteRecursoServidor) {
                        echo "alertaAviso('@Classe/Aplicação do Salário do Servidor já está cadastrada na Lista.','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            $obTTCMBAFonteRecursoServidor = new TTCMBAFonteRecursoServidor();
            $obTTCMBAFonteRecursoServidor->setDado('cod_tipo_fonte', $cmbFonteRecursoServidor);
            $obTTCMBAFonteRecursoServidor->recuperaPorChave($rsFonteRecursoServidor);         
            
            $obTTCMBAFonteRecursoLotacao = new TTCMBAFonteRecursoLotacao();
            $obTTCMBAFonteRecursoLotacao->recuperaFonteRecursoLotacao( $rsFonteRecursoLotacao,  ' WHERE orgao.cod_orgao IN ('.implode(',', $arCodLotacaoSelecionados).')' );
            
            $rsFonteRecursoLocal = new RecordSet();
            
            if (isset($arCodLocalSelecionados)){
                $obTOrganogramaLocal = new TOrganogramaLocal();
                $obTOrganogramaLocal->recuperaTodos( $rsFonteRecursoLocal,  ' WHERE cod_local IN ('.implode(',', $arCodLocalSelecionados).')' );
            }

            if (Sessao::read('arFonteRecursoLotacaoLocal') == "") {
                $inCounter = 0;
                $inOcorrencia = 0;

                $arFonteRecursoLotacaoLocal[$inCounter]['id']             = $inCounter;
                $arFonteRecursoLotacaoLocal[$inCounter]['cod_tipo']       = $inCounter;
                $arFonteRecursoLotacaoLocal[$inCounter]['cod_tipo_fonte'] = $rsFonteRecursoServidor->getCampo('cod_tipo_fonte');
                $arFonteRecursoLotacaoLocal[$inCounter]['descricao']      = $rsFonteRecursoServidor->getCampo('descricao');
                $arFonteRecursoLotacaoLocal[$inCounter]['lotacao']        = $rsFonteRecursoLotacao->arElementos;
                $arFonteRecursoLotacaoLocal[$inCounter]['local']          = $rsFonteRecursoLocal->arElementos;

                Sessao::write('arFonteRecursoLotacaoLocal', $arFonteRecursoLotacaoLocal);
            } else {
                $inCounter = count(Sessao::read('arFonteRecursoLotacaoLocal'));

                $arFonteRecursoLotacaoLocal[$inCounter]['id']             = $inCounter;
                $arFonteRecursoLotacaoLocal[$inCounter]['cod_tipo']       = $inCounter;
                $arFonteRecursoLotacaoLocal[$inCounter]['cod_tipo_fonte'] = $rsFonteRecursoServidor->getCampo('cod_tipo_fonte');
                $arFonteRecursoLotacaoLocal[$inCounter]['descricao']      = $rsFonteRecursoServidor->getCampo('descricao');
                $arFonteRecursoLotacaoLocal[$inCounter]['lotacao']        = $rsFonteRecursoLotacao->arElementos;
                $arFonteRecursoLotacaoLocal[$inCounter]['local']          = $rsFonteRecursoLocal->arElementos;

                Sessao::write('arFonteRecursoLotacaoLocal',$arFonteRecursoLotacaoLocal);
            }

            $stJs  =  montaListaFonteRecursoServidor("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.inCodLotacaoSelecionados','document.frm.inCodLotacaoDisponiveis','tudo');";
            $stJs .= "JavaScript:passaItem('document.frm.inCodLocalSelecionados','document.frm.inCodLocalDisponiveis','tudo');";
            $stJs .= "jq('select#cmbFonteRecursoServidor').selectOptions('');";
            echo "alertaAviso('Classe/Aplicação inserido na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione uma classe/aplicação e pelo menos uma lotação.','form','erro','".Sessao::getId()."');";
        }
        
    break;

    case "excluirFonteRecursoLista":
        $inCount = 0;
        
        if (is_array(Sessao::read('arFonteRecursoLotacaoLocal'))) {
            foreach (Sessao::read('arFonteRecursoLotacaoLocal') as $arFonteRecursoLotacaoLocalTmp ) {
                if ($arFonteRecursoLotacaoLocalTmp["cod_tipo"] != $request->get("inVlTipo")) {
                    $arTmp[$inCount] = $arFonteRecursoLotacaoLocalTmp;
                    $inCount++;
                }
            }
        }

        echo "alertaAviso('Classe/Aplicação do Salário do Servidor excluido.','','info','".Sessao::getId()."');";
        echo "jq('#inCodLocalDisponiveis').focus();";
        
        Sessao::write('arFonteRecursoLotacaoLocal',$arTmp);
        $stJs = montaListaFonteRecursoServidor("mostrar");
        echo $stJs;
    break;
      
    case "detalharFonteRecursoServidor":
        $inCodTipo = $request->get('cod_tipo');

        $rsFonteRecursoLotacao = new RecordSet();
        $rsFonteRecursoLocal   = new RecordSet();
        
        $arFonteRecursoLotacaoLocal = Sessao::read('arFonteRecursoLotacaoLocal');
        
        for ($i = 0 ;$i < count(Sessao::read('arFonteRecursoLotacaoLocal')); $i++) {
            if ($inCodTipo == $arFonteRecursoLotacaoLocal[$i]["cod_tipo"]) {
                $rsFonteRecursoLotacao->preenche($arFonteRecursoLotacaoLocal[$i]["lotacao"]);
                $rsFonteRecursoLocal->preenche($arFonteRecursoLotacaoLocal[$i]["local"]);
                break;
            }
        }
        
        while (!$rsFonteRecursoLotacao->EOF()) {
            $rsFonteRecursoLotacao->setCampo('cod_tipo', $inCodTipo);
            $rsFonteRecursoLotacao->proximo();
        }
        
        $obTableLotacao = new Table;
        $obTableLotacao->setRecordset( $rsFonteRecursoLotacao );
        $obTableLotacao->addLineNumber(false);
        $obTableLotacao->Head->addCabecalho('Lotação', 50);
        $obTableLotacao->Body->addCampo('[cod_estrutural] - [descricao]', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoTipoSalario.php?cod_orgao=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirFonteRecursoLotacao&quot;)";

        $obTableLotacao->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_orgao', 'cod_tipo' ) );

        $obTableLotacao->montaHTML(true);
        $stHTML = $obTableLotacao->getHtml();

        while (!$rsFonteRecursoLocal->EOF()) {
            $rsFonteRecursoLocal->setCampo('cod_tipo', $inCodTipo);
            $rsFonteRecursoLocal->proximo();
        }
        
        $obTableLocal = new Table;
        $obTableLocal->setRecordset( $rsFonteRecursoLocal );
        $obTableLocal->addLineNumber(false);
        $obTableLocal->Head->addCabecalho('Local', 50);
        $obTableLocal->Body->addCampo('[cod_local] - [descricao]', 'E');

        $stTableLocalAction = 'excluir';
        $stFunctionLocalJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoTipoSalario.php?cod_local=%s&cod_tipo=%s";
        $stFunctionLocalJs .= "&quot;,&quot;excluirFonteRecursoLocal&quot;)";

        $obTableLocal->Body->addAcao($stTableLocalAction, $stFunctionLocalJs, array( 'cod_local', 'cod_tipo' ) );

        $obTableLocal->montaHTML(true);
        $stHTML = $stHTML."\n".$obTableLocal->getHtml();

        echo  $stHTML;
    break;

    case "excluirFonteRecursoLotacao":
        $arFonteRecursoLotacaoLocalSessao = Sessao::read('arFonteRecursoLotacaoLocal');

        foreach ($arFonteRecursoLotacaoLocalSessao AS $arFonteRecursoLotacaoLocalTmp) {
            if ($arFonteRecursoLotacaoLocalTmp['cod_tipo'] == $request->get('cod_tipo')) {

                if (count($arFonteRecursoLotacaoLocalTmp['lotacao']) == 1) {
                    echo "alertaAviso('@Não é possível deletar está lotação, pois ele é o única relacionada a esta classe/aplicação do salário do servidor.','form','erro','".Sessao::getId()."');";
                    echo "jq('#inCodLocalDisponiveis').focus();";
                    die;
                }

                foreach ($arFonteRecursoLotacaoLocalTmp['lotacao'] AS $arLotacaoTmp) {
                    if ($arLotacaoTmp['cod_orgao'] != $request->get('cod_orgao')) {
                        $arLotacaoNovo[] = $arLotacaoTmp;
                    }
                }
                $arFonteRecursoLotacaoLocalTmp['lotacao'] = $arLotacaoNovo;
            }
            $arLotacaoNovoSessao[] = $arFonteRecursoLotacaoLocalTmp;
        }

        echo "alertaAviso('Lotação excluida.','','info','".Sessao::getId()."');";
        echo "jq('#inCodLocalDisponiveis').focus();";
        
        Sessao::write('arFonteRecursoLotacaoLocal', $arLotacaoNovoSessao);
        $stJs = montaListaFonteRecursoServidor("mostrar");
        echo $stJs;
    break;

    case "excluirFonteRecursoLocal":
        $arFonteRecursoLotacaoLocalSessao = Sessao::read('arFonteRecursoLotacaoLocal');

        foreach ($arFonteRecursoLotacaoLocalSessao AS $arFonteRecursoLotacaoLocalTmp) {
            if ($arFonteRecursoLotacaoLocalTmp['cod_tipo'] == $request->get('cod_tipo')) {

                foreach ($arFonteRecursoLotacaoLocalTmp['local'] AS $arLocalTmp) {
                    if ($arLocalTmp['cod_local'] != $request->get('cod_local')) {
                        $arLocalNovo[] = $arLocalTmp;
                    }
                }
                $arFonteRecursoLotacaoLocalTmp['local'] = $arLocalNovo;
            }
            $arLocalNovoSessao[] = $arFonteRecursoLotacaoLocalTmp;
        }

        echo "alertaAviso('Local excluido.','','info','".Sessao::getId()."');";
        echo "jq('#inCodLocalDisponiveis').focus();";
        
        Sessao::write('arFonteRecursoLotacaoLocal', $arLocalNovoSessao);
        $stJs = montaListaFonteRecursoServidor("mostrar");
        echo $stJs;
    break;

    case "limparListaFonteRecursoServidor":
            $stJs .= "JavaScript:passaItem('document.frm.inCodLotacaoSelecionados','document.frm.inCodLotacaoDisponiveis','tudo');";
            $stJs .= "JavaScript:passaItem('document.frm.inCodLocalSelecionados','document.frm.inCodLocalDisponiveis','tudo');";
            $stJs .= "jq('select#cmbFonteRecursoServidor').selectOptions('');";
        echo  $stJs;
    break;

    case "fonteRecursoExistentes":
        $obTTCMBAFonteRecursoServidor = new TTCMBAFonteRecursoServidor();
        $obTTCMBAFonteRecursoServidor->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCMBAFonteRecursoServidor->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMBAFonteRecursoServidor->recuperaFonteRecursoLotacaoLocal($rsRecursoLotacaoLocal);
        
        $inCounter = 0;
        $arFonteRecursoLotacaoLocalSessao = array();
        
        foreach ($rsRecursoLotacaoLocal->arElementos as $tipoFonte) {

            $obTTCMBAFonteRecursoLotacao = new TTCMBAFonteRecursoLotacao();
            $obTTCMBAFonteRecursoLotacao->setDado('cod_tipo_fonte' , $tipoFonte['cod_tipo_fonte']);
            $obTTCMBAFonteRecursoLotacao->setDado('cod_entidade'   , Sessao::read('cod_entidade'));
            $obTTCMBAFonteRecursoLotacao->setDado('exercicio'      , Sessao::getExercicio());
            $obTTCMBAFonteRecursoLotacao->recuperaFonteRecursoLotacaoSelecionado($rsFonteRecursoLotacao, ' ORDER BY cod_estrutural ');

            $obTTCMBAFonteRecursoLocal = new TTCMBAFonteRecursoLocal();
            $obTTCMBAFonteRecursoLocal->setDado('cod_tipo_fonte' , $tipoFonte['cod_tipo_fonte']);
            $obTTCMBAFonteRecursoLocal->setDado('cod_entidade'   , Sessao::read('cod_entidade'));
            $obTTCMBAFonteRecursoLocal->setDado('exercicio'      , Sessao::getExercicio());
            $obTTCMBAFonteRecursoLocal->recuperaFonteRecursoLocal($rsFonteRecursoLocal, ' ORDER BY cod_local ');

            $arFonteRecursoLotacaoLocalSessao[$inCounter]['id']             = $inCounter;
            $arFonteRecursoLotacaoLocalSessao[$inCounter]['cod_tipo']       = $inCounter;
            $arFonteRecursoLotacaoLocalSessao[$inCounter]['cod_tipo_fonte'] = $tipoFonte['cod_tipo_fonte'];
            $arFonteRecursoLotacaoLocalSessao[$inCounter]['descricao']      = $tipoFonte['descricao'];
            $arFonteRecursoLotacaoLocalSessao[$inCounter]['lotacao']        = $rsFonteRecursoLotacao->arElementos;
            $arFonteRecursoLotacaoLocalSessao[$inCounter]['local']          = $rsFonteRecursoLocal->arElementos;

            $inCounter++;
        }

        Sessao::write('arFonteRecursoLotacaoLocal', $arFonteRecursoLotacaoLocalSessao);
        $stJs = montaListaFonteRecursoServidor("mostrar");
        
        echo $stJs;
    break;
}

function montaListaFuncaoServidor($stAcao)
{
    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arFuncaoCargoServidor')) > 0) {
            $rsFuncaoCargoServidor = new RecordSet;
            $rsFuncaoCargoServidor->preenche(Sessao::read('arFuncaoCargoServidor'));
            $rsFuncaoCargoServidor->setPrimeiroElemento();

            $obTableTree = new TableTree;
            $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoSalario.php' );
            $obTableTree->setParametros  ( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharListaCargoServidor" );
            $obTableTree->setRecordset   ( $rsFuncaoCargoServidor );
            $obTableTree->setSummary     ( 'Lista de Funções' );
            $obTableTree->setConditional ( true );
            $obTableTree->Head->AddCabecalho( 'Funções',50 );
            $obTableTree->Body->addCampo ( '[cod_tipo_funcao] - [descricao]', 'E' );
            $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFuncaoServidorLista','cod_tipo') );
            $obTableTree->montaHTML      ( true );
            $stHTML = $obTableTree->getHtml();

            $stJs .= "d.getElementById('spnListaFuncaoServidor').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnListaFuncaoServidor').innerHTML = '';\n";
        }

    } else { //Incluir
        $arFuncaoCargoServidorSessao = Sessao::read('arFuncaoCargoServidor');

        for ($inFuncaoCargoServidor = 0; $inFuncaoCargoServidor < count($arFuncaoCargoServidorSessao); $inFuncaoCargoServidor++) {
            $arElementos[] = $arFuncaoCargoServidorSessao[$inFuncaoCargoServidor];
        }

        $rsFuncaoCargoServidor = new RecordSet;
        $rsFuncaoCargoServidor->preenche ( $arElementos );
        $rsFuncaoCargoServidor->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo        ( 'OCManterConfiguracaoTipoSalario.php' );
        $obTableTree->setParametros     ( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharListaCargoServidor" );
        $obTableTree->setRecordset      ( $rsFuncaoCargoServidor );
        $obTableTree->setSummary        ( 'Lista de Funções' );
        $obTableTree->setConditional    ( true );
        $obTableTree->Head->addCabecalho( 'Funções',50 );
        $obTableTree->Body->addCampo    ( '[cod_tipo_funcao] - [descricao]','E' );
        $obTableTree->Body->addAcao     ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFuncaoServidorLista','cod_tipo') );

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnListaFuncaoServidor').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

function montaListaFuncaoServidorTemporario($stAcao)
{
    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arFuncaoCargoServidorTemporario')) > 0) {
            $rsFuncaoCargoServidorTemporario = new RecordSet;
            $rsFuncaoCargoServidorTemporario->preenche(Sessao::read('arFuncaoCargoServidorTemporario'));

            $obTableTree = new TableTree;
            $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoSalario.php' );
            $obTableTree->setParametros  ( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharListaCargoServidorTemporario" );
            $obTableTree->setRecordset   ( $rsFuncaoCargoServidorTemporario );
            $obTableTree->setSummary     ( 'Lista de Funções' );
            $obTableTree->setConditional ( true );
            $obTableTree->Head->AddCabecalho( 'Funções',50 );
            $obTableTree->Body->addCampo ( '[cod_tipo_funcao] - [descricao]', 'E' );
            $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFuncaoServidorListaTemporario','cod_tipo') );
            $obTableTree->montaHTML      ( true );
            $stHTML = $obTableTree->getHtml();

            $rsFuncaoCargoServidorTemporario->setPrimeiroElemento();

            $stJs .= "d.getElementById('spnListaFuncaoServidorTemporario').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnListaFuncaoServidorTemporario').innerHTML = '';\n";
        }

    } else { //Incluir
        $arFuncaoCargoServidorSessaoTemporario = Sessao::read('arFuncaoCargoServidorTemporario');

        for ($inFuncaoCargoServidor = 0; $inFuncaoCargoServidor < count($arFuncaoCargoServidorSessaoTemporario); $inFuncaoCargoServidor++) {
            $arElementos[] = $arFuncaoCargoServidorSessaoTemporario[$inFuncaoCargoServidor];
        }

        $rsFuncaoCargoServidorTemporario = new RecordSet;
        $rsFuncaoCargoServidorTemporario->preenche ( $arElementos );
        $rsFuncaoCargoServidorTemporario->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo        ( 'OCManterConfiguracaoTipoSalario.php' );
        $obTableTree->setParametros     ( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharListaCargoServidorTemporario" );
        $obTableTree->setRecordset      ( $rsFuncaoCargoServidorTemporario );
        $obTableTree->setSummary        ( 'Lista de Funções' );
        $obTableTree->setConditional    ( true );
        $obTableTree->Head->addCabecalho( 'Funções',50 );
        $obTableTree->Body->addCampo    ( '[cod_tipo_funcao] - [descricao]','E' );
        $obTableTree->Body->addAcao     ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFuncaoServidorListaTemporario','cod_tipo') );

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnListaFuncaoServidorTemporario').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

function montaListaBancoEmprestimo($stAcao)
{
    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arBancoEventosEmprestimo')) > 0) {
            $rsBancoEventosEmprestimo = new RecordSet;
            $rsBancoEventosEmprestimo->preenche(Sessao::read('arBancoEventosEmprestimo'));
            $rsBancoEventosEmprestimo->setPrimeiroElemento();

            $obTableTree = new TableTree;
            $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoSalario.php' );
            $obTableTree->setParametros  ( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharListaEventosEmprestimo" );
            $obTableTree->setRecordset   ( $rsBancoEventosEmprestimo );
            $obTableTree->setSummary     ( 'Lista de Códigos de Compensação' );
            $obTableTree->setConditional ( true );
            $obTableTree->Head->AddCabecalho( 'Empréstimos Consignados',50 );
            $obTableTree->Body->addCampo ( '[num_banco] - [nom_banco]', 'E' );
            $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirBancoEmprestimoLista','cod_tipo') );
            $obTableTree->montaHTML      ( true );
            $stHTML = $obTableTree->getHtml();

            $stJs .= "d.getElementById('spnListaBancoEmprestimo').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnListaBancoEmprestimo').innerHTML = '';\n";
        }

    } else { //Incluir
        $arBancoEventosEmprestimoSessao = Sessao::read('arBancoEventosEmprestimo');

        for ($inFuncaoCargoServidor = 0; $inFuncaoCargoServidor < count($arBancoEventosEmprestimoSessao); $inFuncaoCargoServidor++) {
            $arElementos[] = $arBancoEventosEmprestimoSessao[$inFuncaoCargoServidor];
        }

        $rsBancoEventosEmprestimo = new RecordSet;
        $rsBancoEventosEmprestimo->preenche ( $arElementos );
        $rsBancoEventosEmprestimo->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo        ( 'OCManterConfiguracaoTipoSalario.php' );
        $obTableTree->setParametros     ( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharListaEventosEmprestimo" );
        $obTableTree->setRecordset      ( $rsBancoEventosEmprestimo );
        $obTableTree->setSummary        ( 'Lista de Códigos de Compensação' );
        $obTableTree->setConditional    ( true );
        $obTableTree->Head->addCabecalho( 'Empréstimos Consignados',50 );
        $obTableTree->Body->addCampo    ( '[num_banco] - [nom_banco]', 'E' );
        $obTableTree->Body->addAcao     ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirBancoEmprestimoLista','cod_tipo') );

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnListaBancoEmprestimo').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

function montaListaFonteRecursoServidor($stAcao)
{
    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arFonteRecursoLotacaoLocal')) > 0) {
            $rsFonteRecursoLotacaoLocalServidor = new RecordSet;
            $rsFonteRecursoLotacaoLocalServidor->preenche(Sessao::read('arFonteRecursoLotacaoLocal'));
            $rsFonteRecursoLotacaoLocalServidor->setPrimeiroElemento();

            $obTableTree = new TableTree;
            $obTableTree->setArquivo     ( 'OCManterConfiguracaoTipoSalario.php' );
            $obTableTree->setParametros  ( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharFonteRecursoServidor" );
            $obTableTree->setRecordset   ( $rsFonteRecursoLotacaoLocalServidor );
            $obTableTree->setSummary     ( 'Lista de Classe/Aplicação do Salário do Servidor' );
            $obTableTree->setConditional ( true );
            $obTableTree->Head->AddCabecalho( 'Classes/Aplicações',50 );
            $obTableTree->Body->addCampo ( '[cod_tipo_fonte] - [descricao]', 'E' );
            $obTableTree->Body->addAcao  ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFonteRecursoLista','cod_tipo') );
            $obTableTree->montaHTML      ( true );
            $stHTML = $obTableTree->getHtml();

            $stJs .= "d.getElementById('spnListaFonteRecursoServidor').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnListaFonteRecursoServidor').innerHTML = '';\n";
        }

    } else { //Incluir
        $arFonteRecursoLotacaoLocalSessao = Sessao::read('arFonteRecursoLotacaoLocal');

        for ($inFonteRecursoLotacaoLocal = 0; $inFonteRecursoLotacaoLocal < count($arFonteRecursoLotacaoLocalSessao); $inFonteRecursoLotacaoLocal++) {
            $arElementos[] = $arFonteRecursoLotacaoLocalSessao[$inFonteRecursoLotacaoLocal];
        }

        $rsFonteRecursoLotacaoLocalServidor = new RecordSet;
        $rsFonteRecursoLotacaoLocalServidor->preenche ( $arElementos );
        $rsFonteRecursoLotacaoLocalServidor->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo        ( 'OCManterConfiguracaoTipoSalario.php' );
        $obTableTree->setParametros     ( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharFonteRecursoServidor" );
        $obTableTree->setRecordset      ( $rsFonteRecursoLotacaoLocalServidor );
        $obTableTree->setSummary        ( 'Lista de Classe/Aplicação do Salário do Servidor' );
        $obTableTree->setConditional    ( true );
        $obTableTree->Head->addCabecalho( 'Classes/Aplicações',50 );
        $obTableTree->Body->addCampo    ( '[cod_tipo_fonte] - [descricao]','E' );
        $obTableTree->Body->addAcao     ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFonteRecursoLista','cod_tipo') );

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnListaFonteRecursoServidor').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

?>