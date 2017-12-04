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
    * Página Oculta de Homologar PPA
    * Data de Criação: 06/10/2008

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package      URBEM
    * @subpackage   PPA

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ElaborarEstimativaReceita';
$pgOcul     = 'OC'.$stPrograma.".php";
$pgProc     = 'PR'.$stPrograma.".php";
$pgForm     = 'FM'.$stPrograma.".php";
$pgJs       = 'JS'.$stPrograma.".js";

switch ($_POST['stAcao']) {
case "elaborar":

    if (($_POST['flPorcentagemAno1'] != '' && $_POST['flPorcentagemAno2'] != '' && $_POST['flPorcentagemAno3'] != '' && $_POST['flPorcentagemAno4'] != '') || ($_POST['stTipoPercentualInformado'] == 'A')) {

        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            require CAM_GF_PPA_MAPEAMENTO."TPPAPPAEstimativaOrcamentariaBase.class.php";
            $obTPPAPPAEstimativaOrcamentariaBase = new TPPAPPAEstimativaOrcamentariaBase;
            $obTPPAPPAEstimativaOrcamentariaBase->setDado('cod_ppa', $_POST['inCodPPA']);
            $obErro = $obTPPAPPAEstimativaOrcamentariaBase->exclusao($boTransacao);

            $arDados = array('valor'=>'', 'chave'=>'');
            $arValidacao = array(
                    'flValorReceita'   => $arDados
                ,   'percentual_ano_1' => $arDados
                ,   'percentual_ano_2' => $arDados
                ,   'percentual_ano_3' => $arDados
                ,   'percentual_ano_4' => $arDados
            );

            if (!$obErro->ocorreu()) {
                $arPost = $_POST;
                $inUltimoCodReceita = '';
                foreach ($arPost as $stChave => $stValor) {
                    $arChave = explode("_", $stChave);

                    // verifica se quebrou a chave pelo caracter e se o valor na segunda posicao é um A, significando que é uma receita analítica
                    if ((count($arChave) > 1 && $arChave[2] == 'A') || ($arChave[1] == 26)) {

                        if ($inUltimoCodReceita != $arChave[1] && $inUltimoCodReceita != '') {
                            $obTPPAPPAEstimativaOrcamentariaBase->setDado('cod_receita', $inUltimoCodReceita);
                            $obTPPAPPAEstimativaOrcamentariaBase->setDado('tipo_percentual_informado', $_POST['stTipoPercentualInformado']);
                            $obErro = $obTPPAPPAEstimativaOrcamentariaBase->inclusao($boTransacao);

                            if ($obErro->ocorreu()) {
                                break;
                            }
                        }

                        if ($arChave[0] == 'flValorReceita') {
                            $arValidacao['flValorReceita']['valor'] = $stValor;
                            $arValidacao['flValorReceita']['chave'] = $stChave;
                            $obTPPAPPAEstimativaOrcamentariaBase->setDado('valor', $stValor);
                        } else {
                            $flValor = str_replace(',', '.',str_replace('.', '',$stValor));
                            $arValidacao['percentual_ano_'.$arChave[0][5]]['valor'] = $flValor;
                            $arValidacao['percentual_ano_'.$arChave[0][5]]['chave'] = $stChave;
                            $obTPPAPPAEstimativaOrcamentariaBase->setDado('percentual_ano_'.$arChave[0][5], $flValor);
                        }

                        $inUltimoCodReceita = $arChave[1];
                    }
                }

                if ($obErro->ocorreu()) {
                    // REALIZA A VALIDAÇÃO DOS DADOS PARA INFORMAR AO USUÁRIO QUAL O CAMPO QUE ESTÁ COM O FORMATO INVÁVIDO
                    $obTransacao->rollbackAndClose();
                    echo '<script>alertaAviso("'.getMensagemValidaCampos($arValidacao).'","n_excluir","erro","'.Sessao::getId().'","")</script>';

                } else {
                    $obTransacao->commitAndClose();
                    sistemaLegado::alertaAviso($pgForm.'?stAcao=elaborar', 'PPA '.$_POST['inCodPPA'], 'excluir', 'aviso', Sessao::getId());
                }

            } else {
                $obTransacao->rollbackAndClose();
                sistemaLegado::alertaAviso($pgForm.'?stAcao=elaborar', 'Ocorreu um erro ao incluir a Elaboração de Estimativa da Receita.', 'n_excluir', 'erro', Sessao::getId());
            }

        } else {
            $obTransacao->rollbackAndClose();
            sistemaLegado::alertaAviso($pgForm.'?stAcao=elaborar', 'Ocorreu um erro abrir a transação com o banco de dados.', 'n_excluir', 'erro', Sessao::getId());
        }

     } else {
         echo '<script>alertaAviso("É necessário preencher os 4 campos de porcentagem dos anos","n_excluir","erro","'.Sessao::getId().'","")</script>';
     }
    break;
}

function getMensagemValidaCampos($arValidacao)
{
    $arReturn = SistemaLegado::validaMascaraDinamica('9.999.999.999,99', $arValidacao['flValorReceita']['valor']);
    if ($arReturn[0] == 0) {
        $arChave = explode('_', $arValidacao['flValorReceita']['chave']);
        $stMensagem = 'O valor do campo Valor na linha '.$arChave[1].' está fora dos padrões, digite-o corretamente.';
    } else {
        array_shift($arValidacao);
        foreach ($arValidacao as $stChave => $arDados) {
            $arChave = explode('_', $arDados['chave']);
            $arReturn = SistemaLegado::validaMascaraDinamica('999999.99', $arDados['valor']);
            if ($arReturn[0] == 0) {
                $stMensagem = 'O valor do campo Ano '.$arChave[0][5].' (%) na linha '.$arChave[1].' está fora dos padrões, digite-o corretamente.';
                break;
            }
        }
    }

    return $stMensagem;
}

?>
