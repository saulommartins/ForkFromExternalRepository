<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 20112 Confederação Nacional de Municípos                         *
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
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-11201, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 * Arquivo oculto - Configuracao - Anexo RREO 12
 *
 * @category    Urbem
 * @package     STN
 * @author      Carlos Adriano   <carlos.silva@cnm.org.br>
 * $Id: OCConfigurarRREO12.php 66695 2016-11-28 20:46:59Z carlos.silva $
 */

//inclui os arquivos necessarios
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_GPC_STN_MAPEAMENTO  . 'TSTNVinculoSaudeRREO12.class.php';
include CAM_FW_COMPONENTES . 'Table/TableTree.class.php';

$stAcao = $_REQUEST["stCtrl"];

switch ($stAcao) {

    case 'incluirReceitaAnexo12':
        $obErro = new Erro();
        $arReceitas = (array) Sessao::read('receitas');

        if ($_REQUEST['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercício');
        } elseif ($_REQUEST['inCodReceita'] == '') {
            $obErro->setDescricao('Informe a Receita');
        }

        if (!$obErro->ocorreu()) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $_REQUEST['inCodReceita'])) {
                    $obErro->setDescricao('A Receita já está na lista');
                }
            }
        }

        if (!$obErro->ocorreu()) {
            include CAM_GF_ORC_NEGOCIO . 'ROrcamentoReceita.class.php';
            $obROrcamentoReceita = new ROrcamentoReceita();
            $obROrcamentoReceita->setExercicio($_REQUEST['stExercicio']);
            $obROrcamentoReceita->setCodReceita($_REQUEST['inCodReceita']);
            $obROrcamentoReceita->listar($rsReceita);

            $arReceitas[] = array(
                'exercicio'   => $_REQUEST['stExercicio'],
                'cod_receita' => $_REQUEST['inCodReceita'],
                'descricao'   => $rsReceita->getCampo('descricao'),
                'new'         => true
            );
            $arReceitas = Sessao::write('receitas',$arReceitas);

            $stJs.= "jq('input#inCodReceita').val('');";
            $stJs.= "jq('#stNomReceita').html('&nbsp;');";
            $stJs.= buildListaReceitaAnexo12($arReceitas);
        } else {
            $stJs.= "alertaAviso('" . $obErro->getDescricao() . ".','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    break;

    case 'excluirReceitaAnexo12':
        $arReceitas    = (array) Sessao::read('receitas');
        $arReceitasDel = (array) Sessao::read('receitas_del');
        $arReceitasNew = array();
        
        if ($_REQUEST['cod_receita']) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $_REQUEST['cod_receita'])) {
                    $arReceitasDel[] = $arReceita;
                } else {
                    $arReceitasNew[] = $arReceita;
                }
            }
        } else {
            $arReceitasNew = array();
        }
        Sessao::write('receitas'    ,$arReceitasNew);
        Sessao::write('receitas_del',$arReceitasDel);

        $stJs.= buildListaReceitaAnexo12($arReceitasNew);

        echo $stJs;
    break;

    case 'carregaReceitasAnexo12':
        $obTSTNVinculoSaudeRREO12 = new TSTNVinculoSaudeRREO12;
        $obTSTNVinculoSaudeRREO12->recuperaRelacionamento($rsReceitas);
        
        while (!$rsReceitas->eof()) {
            $arReceitas[] = array (
                'exercicio'   => $rsReceitas->getCampo('exercicio'),
                'cod_receita' => $rsReceitas->getCampo('cod_receita'),
                'descricao'   => $rsReceitas->getCampo('descricao'),
                'new'         => false,
            );

            $rsReceitas->proximo();
        }

        Sessao::write('receitas',$arReceitas);
        $stJs .= buildListaReceitaAnexo12($arReceitas);

        echo $stJs;
    break;
}


function buildListaReceitaAnexo12($arDados)
{
    $rsReceita = new RecordSet;
    $rsReceita->preenche($arDados);

    $table = new Table;
    $table->setRecordset  ($rsReceita);
    $table->setSummary    ('Receitas Vinculadas');

    $table->Head->addCabecalho('Exercicio', 10);
    $table->Head->addCabecalho('Receita'  , 70);

    $table->Body->addCampo('exercicio'                  ,'C');
    $table->Body->addCampo('[cod_receita] - [descricao]','E');

    $table->Body->addAcao('excluir', "ajaxJavaScript('OCConfigurarRREO12.php?&cod_receita=%s','excluirReceitaAnexo12');", array('cod_receita'));

    $table->montaHTML(true);

    $stJs.= "\n jq('#spnLista').html('".$table->getHtml()."');";

    return $stJs;
}