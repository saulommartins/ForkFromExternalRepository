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
    * Titulo do arquivo : Arquivo de processamento do TCE-AL Relacionar Margem Consignada
    * Data de Criação: 26/09/2014

    * @author Analista
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRManterMargemConsignada.php 60066 2014-09-26 18:45:51Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ManterMargemConsignada';
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
    $stSessaoEntidade = Sessao::getEntidade();
    Sessao::setEntidade(Sessao::read('stEntidadeSessao'));
    
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado("cod_modulo" , '62');
    $obTAdministracaoConfiguracao->setDado("parametro"  , 'tceal_config_margem_consignada'.Sessao::getEntidade());
    $obTAdministracaoConfiguracao->recuperaPorChave($rsRecordSet);
    
    if($rsRecordSet->getNumLinhas()>0){    
      $stSql  = "UPDATE administracao.configuracao                                 \n";
      $stSql .= "   SET valor      = '" . $stEventosSelecionados . "'              \n";
      $stSql .= " WHERE cod_modulo = 62                                            \n";
      $stSql .= "   AND parametro  = 'tceal_config_margem_consignada".Sessao::getEntidade()."'  \n";
      $stSql .= "   AND exercicio  = '" . $exercicio . "';                         \n";
    }else{
      $stSql  = "INSERT INTO administracao.configuracao                                \n";
      $stSql .= " (valor, cod_modulo, parametro, exercicio)                            \n";
      $stSql .= " VALUES ('".$stEventosSelecionados."'                                 \n";
      $stSql .= "          , 62                                                        \n";
      $stSql .= "          , 'tceal_config_margem_consignada".Sessao::getEntidade()."' \n";
      $stSql .= "          , '".$exercicio."')                                         \n";
    }

    $dbConfiguracao = new dataBaseLegado;
    $dbConfiguracao->abreBd();
    $dbConfiguracao->abreSelecao($stSql);
    
    Sessao::setEntidade($stSessaoEntidade);

    if ($dbConfiguracao->pegaUltimoErro() == '') {
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso('Erro ao incluir configuração' ,"n_incluir","erro");
    }
    $dbConfiguracao->fechaBD();
}

Sessao::encerraExcecao();
