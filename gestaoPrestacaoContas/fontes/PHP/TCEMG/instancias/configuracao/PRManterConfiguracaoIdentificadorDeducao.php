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
  * Página de Processamento de Vinculação da Receita com Identificador Peculiar - TCE - MG
  * Data de Criação   : 17/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: PRManterConfiguracaoIdentificadorDeducao.php 59612 2014-09-02 12:00:51Z gelson $
  *
  * $Revision: 59612 $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGReceitaIndentificadoresPeculiarReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoIdentificadorDeducao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTCEMGReceitaIndentificadoresPeculiarReceita = new TTCEMGReceitaIndentificadoresPeculiarReceita;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTTCEMGReceitaIndentificadoresPeculiarReceita );

if (isset($_REQUEST['stAcao'])) {
    $stAcao = $request->get('stAcao');
} else {
    $stAcao = 'incluir';
}

switch( $stAcao ):
case 'incluir':
default:
    foreach ($_REQUEST as $key=>$value) {
        if (strstr($key,'inCodIdentificador')) {
            $arDados = explode("_",$key);
        $obTTCEMGReceitaIndentificadoresPeculiarReceita->setDado('cod_receita', $arDados[1]);
        $obTTCEMGReceitaIndentificadoresPeculiarReceita->setDado('cod_identificador', $value);
        $obTTCEMGReceitaIndentificadoresPeculiarReceita->setDado('exercicio', Sessao::getExercicio());
            $obTTCEMGReceitaIndentificadoresPeculiarReceita->recuperaPorChave($rsRecordSet);
            if ( !$rsRecordSet->eof() )
                $obTTCEMGReceitaIndentificadoresPeculiarReceita->alteracao();
            else
                $obTTCEMGReceitaIndentificadoresPeculiarReceita->inclusao();
        }
    }
    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=$stAcao",$obTTCEMGReceitaIndentificadoresPeculiarReceita->getDado('cod_receita'), "incluir","aviso", Sessao::getId(), "../");
break;
endswitch;
Sessao::encerraExcecao();
?>
