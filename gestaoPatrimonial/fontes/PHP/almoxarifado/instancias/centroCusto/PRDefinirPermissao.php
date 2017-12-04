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
    * Classe de mapeamento da tabela ALMOXARIFADO.CENTRO_CUSTO
    * Data de Criação: 27/10/2005

    * @author Analista     : Diego
    * @author Desenvolvedor: Rodrigo Schreiner

    * Casos de uso: uc-03.03.07
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php");

$stPrograma = "DefinirPermissao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obRegra = new RAlmoxarifadoPermissaoCentroDeCustos();

$obErro = new Erro;

switch ($stAcao) {

    case "salvar":
        $inNumCGM = $_POST["inNumCGM"];

        if (isset($inNumCGM)) {
            $obRegra->obRCGMPessoaFisica->setNumCGM($inNumCGM);
            // Exclui todos os centros de custos vinculados ao usuário para inserir as novas opções.
            $obErro = $obRegra->excluirTodosCentrosCustos();

            if (!$obErro->ocorreu()) {
                foreach ($_POST as $stChave => $stValor) {
                    $inResult = strpos($stChave, 'boPermissao');
                    if ($inResult !== false) {
                        list($stNomeObjeto, $inCodCentro) = explode("_", $stChave);
                        $obRegra->setCentroCusto($inCodCentro);
                        // Insere os novos vínculos de usuário com centro de custo.
                        $obErro = $obRegra->salvarCentroCusto();
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $obRegra->obRCGMPessoaFisica->consultar($rsDummy);
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),'CGM: '.$obRegra->obRCGMPessoaFisica->getNumCGM().' - '.$obRegra->obRCGMPessoaFisica->getNomCGM(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>
