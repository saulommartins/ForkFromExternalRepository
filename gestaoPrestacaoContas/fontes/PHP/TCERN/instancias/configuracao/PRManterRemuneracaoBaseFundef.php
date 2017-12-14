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
    * Titulo do arquivo : Arquivo de processamento do TCE-RN Configurar Remuneração Base Fundef
    * Data de Criação: 22/07/2013

    * @author Analista:
    * @author Desenvolvedor Tallis

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterRemuneracaoBaseFundef';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');
$exercicio = Sessao::getExercicio();
Sessao::setTrataExcecao(true);

//lotação - local
if (count($_POST['inCodEventoSelecionados']) > 0) {
   $stEventosSelecionados = implode(',', $_POST['inCodEventoSelecionados']);
}

switch ($stAcao) {
case 'configurar' :

    $stSql  = "UPDATE administracao.configuracao                             \n";
    $stSql .= "   SET valor      = '" . $stEventosSelecionados . "'          \n";
    $stSql .= " WHERE cod_modulo = 49                                        \n";
    $stSql .= "   AND parametro  = 'remuneracao_base_fundef".Sessao::getEntidade()."'                 \n";
    $stSql .= "   AND exercicio  = '" . $exercicio . "';                     \n";

    $dbConfiguracao = new dataBaseLegado;
    $dbConfiguracao->abreBd();
    $dbConfiguracao->abreSelecao($stSql);

    if ($dbConfiguracao->pegaUltimoErro() == '') {
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso('Erro ao incluir configuração' ,"n_incluir","erro");
    }
    $dbConfiguracao->fechaBD();
}

Sessao::encerraExcecao();
