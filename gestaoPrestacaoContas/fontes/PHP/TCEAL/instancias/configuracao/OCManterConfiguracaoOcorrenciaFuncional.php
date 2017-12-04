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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 17/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once (CAM_GRH_PES_MAPEAMENTO.'TPessoalAssentamentoAssentamento.class.php');
include_once (CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOcorrenciaFuncional.class.php');
include_once (CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOcorrenciaFuncionalAssentamento.class.php');

$stPrograma = "ManterConfiguracaoOcorrenciaFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

switch ($stCtrl) {
    case "incluirAssentamentoLista":
        $boIncluir = true;
        unset($inCounter);

        $cmbOcorrenciaFuncional      = $_REQUEST['cmbOcorrenciaFuncional'];
        $arAssentamentosSelecionados = $_REQUEST['arAssentamentosSelecionados'];

        if ($cmbOcorrenciaFuncional != '' && $arAssentamentosSelecionados != '') {
            $arOcorrenciasSessao = Sessao::read('arOcorrencias');

            if (is_array($arOcorrenciasSessao)) {
                foreach ($arOcorrenciasSessao as $arOcorrenciasTmp) {
                    if ($arOcorrenciasTmp['cod_ocorrencia'] == $cmbOcorrenciaFuncional) {
                        echo "alertaAviso('@Esta ocorrência já está cadastrada na Lista de Ocorrência(s).','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            $obTTCEALConfiguracaoOcorrenciaFuncional = new TTCEALConfiguracaoOcorrenciaFuncional();
            $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('cod_ocorrencia', $cmbOcorrenciaFuncional);
            $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('exercicio', Sessao::getExercicio());
            $obTTCEALConfiguracaoOcorrenciaFuncional->recuperaPorChave($rsOcorrenciaFuncional);

            $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
            $obTPessoalAssentamentoAssentamento->recuperaTodos($rsPessoalAssentamentoAssentamento, ' WHERE cod_assentamento IN ('.implode(',', $arAssentamentosSelecionados).')');

            if (Sessao::read('arOcorrencias')=="") {
                $inCounter = 0;
                $inOcorrencia = 0;

                $arOcorrenciasSessao[$inCounter]['id']             = $inCounter;
                $arOcorrenciasSessao[$inCounter]['cod_tipo']       = $inCounter;
                $arOcorrenciasSessao[$inCounter]['cod_ocorrencia'] = $rsOcorrenciaFuncional->getCampo('cod_ocorrencia');
                $arOcorrenciasSessao[$inCounter]['descricao']      = $rsOcorrenciaFuncional->getCampo('descricao');
                $arOcorrenciasSessao[$inCounter]['assentamentos']  = $rsPessoalAssentamentoAssentamento->arElementos;

                Sessao::write('arOcorrencias', $arOcorrenciasSessao);

            } else {
                $inCounter = count(Sessao::read('arOcorrencias'));

                $obTTCEALConfiguracaoOcorrenciaFuncional = new TTCEALConfiguracaoOcorrenciaFuncional();
                $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('cod_ocorrencia', $cmbOcorrenciaFuncional);
                $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('exercicio', Sessao::getExercicio());
                $obTTCEALConfiguracaoOcorrenciaFuncional->recuperaPorChave($rsOcorrenciaFuncional);

                $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
                $obTPessoalAssentamentoAssentamento->recuperaTodos($rsPessoalAssentamentoAssentamento, ' WHERE cod_assentamento IN ('.implode(',', $arAssentamentosSelecionados).')');

                $arOcorrenciasSessao[$inCounter]['id']             = $inCounter;
                $arOcorrenciasSessao[$inCounter]['cod_tipo']       = $inCounter;
                $arOcorrenciasSessao[$inCounter]['cod_ocorrencia'] = $rsOcorrenciaFuncional->getCampo('cod_ocorrencia');
                $arOcorrenciasSessao[$inCounter]['descricao']      = $rsOcorrenciaFuncional->getCampo('descricao');
                $arOcorrenciasSessao[$inCounter]['assentamentos']  = $rsPessoalAssentamentoAssentamento->arElementos;

                Sessao::write('arOcorrencias',$arOcorrenciasSessao);
            }

            $stJs  =  montaListaOcorrencias("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.arAssentamentosSelecionados','document.frm.arAssentamentosDisponiveis','tudo');";
            $stJs .= "jq('select#cmbOcorrenciaFuncional').selectOptions('');";
            echo "alertaAviso('Ocorrência e assentamento(s) inseridos na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione uma ocorrência e pelo menos um assentamento.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirOcorrenciaLista":
        $inCount = 0;
        foreach (Sessao::read('arOcorrencias') as $arOcorrenciasTmp ) {
            if ($arOcorrenciasTmp["cod_tipo"] != $_REQUEST["inVlTipo"]) {
                $arTmp[$inCount] = $arOcorrenciasTmp;
                $inCount++;
            }
        }

        echo "alertaAviso('Ocorrência Funcional deletada.','','info','".Sessao::getId()."');";

        Sessao::write('arOcorrencias',$arTmp);
        $stJs = montaListaOcorrencias("mostrar");
        echo $stJs;
    break;

    case "excluirAssentamento":
        $arOcorrenciasSessao = Sessao::read('arOcorrencias');

        foreach ($arOcorrenciasSessao AS $arOcorrenciasTmp) {
            if ($arOcorrenciasTmp['cod_tipo'] == $_REQUEST['cod_tipo']) {

                if (count($arOcorrenciasTmp['assentamentos']) == 1) {
                    echo "alertaAviso('@Não é possível deletar este assentamento, pois ele é o único relacionado a esta ocorrência.','form','erro','".Sessao::getId()."');";
                    die;
                }

                foreach ($arOcorrenciasTmp['assentamentos'] AS $arOcorrenciaTmp) {
                    if ($arOcorrenciaTmp['cod_assentamento'] != $_REQUEST['cod_assentamento']) {
                        $arOcorrenciaNova[] = $arOcorrenciaTmp;
                    }
                }
                $arOcorrenciasTmp['assentamentos'] = $arOcorrenciaNova;
            }
            $arOcorrenciasNovasSessao[] = $arOcorrenciasTmp;
        }

        echo "alertaAviso('Assentamento deletado.','','info','".Sessao::getId()."');";

        Sessao::write('arOcorrencias',$arOcorrenciasNovasSessao);
        $stJs = montaListaOcorrencias("mostrar");
        echo $stJs;
    break;

    case "limparOcorrenciasLista":
            $stJs .= "JavaScript:passaItem('document.frm.arAssentamentosSelecionados','document.frm.arAssentamentosDisponiveis','tudo');";
            $stJs .= "jq('select#cmbOcorrenciaFuncional').selectOptions('');";
        echo  $stJs;
    break;

    case "buscaOcorrenciasAssentamentos":
        $boListar = false;

        $arOcorrencias =  Sessao::read('arOcorrencias');
        for ($i = 0 ;$i < count($arOcorrencias); $i++) {
            $rsOcorrencias = new RecordSet ;
            $rsOcorrencias->preenche($arOcorrencias[$i]["assentamentos"]);
            $boListar = true;
        }
        echo  $stJs;
    break;

    case "ocorrenciasExistentes":
        $rsOcorrencia = new RecordSet;
        $obTTCEALConfiguracaoOcorrenciaFuncional = new TTCEALConfiguracaoOcorrenciaFuncional();
        $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('cod_entidade', Sessao::read('cod_entidade'));
        $obTTCEALConfiguracaoOcorrenciaFuncional->setDado('exercicio', Sessao::getExercicio());
        $obTTCEALConfiguracaoOcorrenciaFuncional->buscarOcorrencias($rsOcorrencia);

        $inCounter = 0;
        $arOcorrenciasSessao = array();

        foreach ($rsOcorrencia->arElementos as $ocorrencia) {
            $rsAssentamento = new RecordSet;
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento = new TTCEALConfiguracaoOcorrenciaFuncionalAssentamento();
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_ocorrencia', $ocorrencia['cod_ocorrencia']);
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_entidade', Sessao::read('cod_entidade'));
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('exercicio', Sessao::getExercicio());
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->buscarAssentamentos($rsAssentamento);

            $arOcorrenciasSessao[$inCounter]['id']             = $inCounter;
            $arOcorrenciasSessao[$inCounter]['cod_tipo']       = $inCounter;
            $arOcorrenciasSessao[$inCounter]['cod_ocorrencia'] = $ocorrencia['cod_ocorrencia'];
            $arOcorrenciasSessao[$inCounter]['descricao']      = $ocorrencia['descricao'];
            $arOcorrenciasSessao[$inCounter]['assentamentos']  = $rsAssentamento->arElementos;

            $inCounter++;
        }

        Sessao::write('arOcorrencias', $arOcorrenciasSessao);

        $stJs = montaListaOcorrencias("mostrar");
        echo $stJs;
    break;

    case "detalharLista":
        $inCodTipo = $_REQUEST['cod_tipo'];

        $arOcorrencias =  Sessao::read('arOcorrencias');
        for ($i = 0 ;$i < count(Sessao::read('arOcorrencias')); $i++) {
            if ($inCodTipo == $arOcorrencias[$i]["cod_tipo"]) {
                $rsOcorrencias = new RecordSet ;
                $rsOcorrencias->preenche($arOcorrencias[$i]["assentamentos"]);
                break;
            }
        }

        while (!$rsOcorrencias->EOF()) {
            $rsOcorrencias->setCampo('cod_tipo', $inCodTipo);
            $rsOcorrencias->proximo();
        }

        $obTable = new Table;
        $obTable->setRecordset($rsOcorrencias);
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Assentamentos', 50);
        $obTable->Body->addCampo('descricao', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterConfiguracaoOcorrenciaFuncional.php?cod_assentamento=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirAssentamento&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_assentamento', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;
}

function montaListaOcorrencias($stAcao)
{
    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arOcorrencias'))>0) {
            $rsFontesPagadoras = new RecordSet;
            $rsFontesPagadoras->preenche(Sessao::read('arOcorrencias'));

            $obTableTree = new TableTree;
            $obTableTree->setArquivo('OCManterConfiguracaoOcorrenciaFuncional.php');
            $obTableTree->setParametros( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharLista");
            $obTableTree->setRecordset($rsFontesPagadoras);
            $obTableTree->setSummary('Lista de Fonte(s) Pagadora(s)');
            $obTableTree->setConditional(true);
            $obTableTree->Head->AddCabecalho('Fontes Pagadoras',50);
            $obTableTree->Body->addCampo( 'descricao', 'E' );
            $obTableTree->Body->addAcao('excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirOcorrenciaLista','cod_tipo'));
            $obTableTree->montaHTML(true);
            $stHTML = $obTableTree->getHtml();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $rsFontesPagadoras->setPrimeiroElemento();

            $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
        }

    } else { //Incluir
        $arOcorrenciasSessao = Sessao::read('arOcorrencias');

        for ($inOcorrencia = 0 ; $inOcorrencia < count($arOcorrenciasSessao);$inOcorrencia++) {
            $arElementos[] = $arOcorrenciasSessao[$inOcorrencia];
        }

        $rsOcorrencias = new RecordSet;
        $rsOcorrencias->preenche ( $arElementos );
        $rsOcorrencias->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo('OCManterConfiguracaoOcorrenciaFuncional.php');
        $obTableTree->setParametros( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharLista");
        $obTableTree->setRecordset($rsOcorrencias);
        $obTableTree->setSummary('Lista de Ocorrência(s)');
        $obTableTree->setConditional(true);
        $obTableTree->Head->addCabecalho('Ocorrências funcionais',50);
        $obTableTree->Body->addCampo('descricao','E');
        $obTableTree->Body->addAcao('excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirOcorrenciaLista','cod_tipo'));

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";
    }

    return $stJs;
}
