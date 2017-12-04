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
    * Arquivo de processamento de unidade orcamentaria da GF e organograma
    * Data de Criação: 22/10/2015
    
    * @author Analista Dagiane Vieira 
    * @author Desenvolvedor Lisiane da Rosa Morais  
    
    * @package URBEM
    * @subpackage
    
    * @ignore

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalDeParaLotacaoOrgaoTCMBA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'RelacionarLotacoesOrgaos';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao(true);

switch ($stAcao) {
case 'configurar' :
    $obErro = new Erro;
    $obTransacao = new Transacao;
    $obTransacao->abreTransacao($boFlag, $boTransacao);

    $arDados = $_POST;
    unset($arDados['stAcao']);

    $rsOrgaoUnidade = new RecordSet;

    // É realizado o processo de apagar todos os dados da tabela para inserir novamente
    $obTPessoalDeParaLotacaoOrgaoTCMBA = new TPessoalDeParaLotacaoOrgaoTCMBA();
    $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('stEntidade' , Sessao::getEntidade());
    $obTPessoalDeParaLotacaoOrgaoTCMBA->recuperaTodos($rsLotacaoOrgao, " WHERE de_para_lotacao_orgao.exercicio = '".Sessao::getExercicio()."'");

    while (!$rsLotacaoOrgao->eof()) {
        $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('cod_orgao'  , $rsLotacaoOrgao->getCampo('cod_orgao'));
        $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('num_orgao'  , $rsLotacaoOrgao->getCampo('num_orgao'));
        $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('exercicio'  , $rsLotacaoOrgao->getCampo('exercicio'));
        $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('stEntidade' , Sessao::getEntidade());
        $boErro = $obTPessoalDeParaLotacaoOrgaoTCMBA->exclusao($boTransacao);
        if ($obErro->ocorreu()) {
            break;
        }
        $rsLotacaoOrgao->proximo();
    }

    if (!$obErro->ocorreu()) {
        foreach ($arDados as $stKey => $stValue) {
            if($stValue != '') {
                $arValue = explode('_', $stValue);
                $arKey = explode('_', $stKey);
                
                $inCodOrgao = $arKey[1];
                $inNumOrgao = $arValue[0];
           
                $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('cod_orgao'  , $inCodOrgao);
                $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('num_orgao'  , $inNumOrgao);
                $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('exercicio'  , Sessao::getExercicio());
                $obTPessoalDeParaLotacaoOrgaoTCMBA->setDado('stEntidade' , Sessao::getEntidade());

                $obErro = $obTPessoalDeParaLotacaoOrgaoTCMBA->inclusao($boTransacao);
            }
            if ($obErro->ocorreu()) {
                break;
            }
        }
    }
    

    if (!$obErro->ocorreu()) {
        $obTransacao->commitAndClose();
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");

    } else {
        $obTransacao->rollbackAndClose();
        sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
    }
}

Sessao::encerraExcecao();
