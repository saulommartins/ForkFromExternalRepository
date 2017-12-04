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
    * Pacote de configuração do TCETO - Processamento Configurar Identificador de Dedução
    * Data de Criação   : 07/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterConfiguracaoIdentificadorDeducao.php 60673 2014-11-07 15:10:07Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOReceitaIndentificadoresPeculiarReceita.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoIdentificadorDeducao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTCETOReceitaIndentificadoresPeculiarReceita = new TTCETOReceitaIndentificadoresPeculiarReceita;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTTCETOReceitaIndentificadoresPeculiarReceita );

if (isset($_REQUEST['stAcao']))
    $stAcao = $request->get('stAcao');
else
    $stAcao = 'incluir';

switch( $stAcao ):
    case 'incluir':
    default:
        foreach ($_REQUEST as $key=>$value) {
            if (strstr($key,'inCodIdentificador')) {
                $arDados = explode("_",$key);                    
                $obTTCETOReceitaIndentificadoresPeculiarReceita->setDado('cod_receita'      , $arDados[1]           );
                $obTTCETOReceitaIndentificadoresPeculiarReceita->setDado('cod_identificador', $value                );
                $obTTCETOReceitaIndentificadoresPeculiarReceita->setDado('exercicio'        , Sessao::getExercicio());
                $obTTCETOReceitaIndentificadoresPeculiarReceita->recuperaPorChave($rsRecordSet);
                if ( !$rsRecordSet->eof() )
                    $obTTCETOReceitaIndentificadoresPeculiarReceita->alteracao();
                else
                    $obTTCETOReceitaIndentificadoresPeculiarReceita->inclusao();
                
                if(isset($stCodReceita))
                    $stCodReceita .= ", ".$arDados[1];
                else
                    $stCodReceita  = $arDados[1];
            }
        }
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=$stAcao","Receitas: ".$stCodReceita, "incluir","aviso", Sessao::getId(), "../");
    break;
endswitch;
Sessao::encerraExcecao();
?>
