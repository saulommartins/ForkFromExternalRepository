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
    * Titulo do arquivo : Arquivo de processamento de Consideracoes de Arquivos do TCM para o URBEM
    * Data de Criação   : 25/02/204

    * @author Analista      Sergio Santos
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    $Id: PRManterConsideracao.php 62857 2015-06-30 13:53:56Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConsideracaoArquivoDescricao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConsideracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');
Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'manter' :

            $TTCEMGConsideracaoArquivoDescricao = new TTCEMGConsideracaoArquivoDescricao();
            foreach ($_REQUEST as $stKey=>$stValue) {
                $arCodigo = explode('_',$stKey); //Formato: stConsideracao_1_IDE
                if ($arCodigo[0]=='stConsideracao') {
                    $stConsideracao = $_REQUEST['stConsideracao_'.$arCodigo[1]."_".$arCodigo[2]];
                    $TTCEMGConsideracaoArquivoDescricao->setDado('cod_arquivo' , $arCodigo[1] );
                    $TTCEMGConsideracaoArquivoDescricao->setDado('periodo'     , (int)$request->get('inMes') );
                    $TTCEMGConsideracaoArquivoDescricao->setDado('cod_entidade', $request->get('inCodEntidade') );
                    $TTCEMGConsideracaoArquivoDescricao->setDado('exercicio'   , Sessao::getExercicio() );
                    $TTCEMGConsideracaoArquivoDescricao->setDado('modulo_sicom', $request->get('stTipoExportacao') );
                    $TTCEMGConsideracaoArquivoDescricao->setDado('descricao'   , $stConsideracao );

                    $TTCEMGConsideracaoArquivoDescricao->recuperaPorChave($rsRecordSet);

                    if ($rsRecordSet->eof()) {
                        $TTCEMGConsideracaoArquivoDescricao->inclusao($boTransacao);
                    } else {
                        $TTCEMGConsideracaoArquivoDescricao->alteracao($boTransacao);
                    }
                }
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    break;
}

Sessao::encerraExcecao();
