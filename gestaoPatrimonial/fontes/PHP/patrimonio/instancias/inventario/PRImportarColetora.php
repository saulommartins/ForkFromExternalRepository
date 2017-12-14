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
    * Arquivo de processamento
    *
    *
    * @date 09/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_POST['stAcao'] ? $_POST['stAcao'] : $_GET['stAcao'];

/**
* @description Valida um arquivo de entrada conforme layout especificado
* @params $file
**/
function validarArquivo($file)
{
    $dado = array();
    $erro = array();
    $numLinha = 1;
    $handle = @fopen($file,'r');

    if ($handle) {
        while ( !feof($handle) ) {
            $linha = fgets($handle, 8096);
            if ($linha != '--- TCE ---') {
                if ( is_numeric(substr($linha, 0, 1)) ) {
                    $stColetoraDigitosLocal = sistemaLegado::pegaConfiguracao( 'coletora_digitos_local', 6);
                    $stColetoraDigitosPlaca = sistemaLegado::pegaConfiguracao( 'coletora_digitos_placa',6);
                    $stCaracterSeparador = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_separador'");
                    if (isset($stCaracterSeparador) && $stCaracterSeparador == '' ) {
                        $tmp['cod_local'] = trim(substr($linha,0,$stColetoraDigitosLocal));
                        $tmp['num_placa'] = trim(substr($linha,$stColetoraDigitosLocal,$stColetoraDigitosPlaca));
                    } else {
                        $tmp['cod_local'] = trim(substr($linha,0,$stColetoraDigitosLocal));
                        $tmp['num_placa'] = trim(substr($linha,strpos($linha,$stCaracterSeparador)+1,$stColetoraDigitosPlaca));
                    }

                    array_push($dado, $tmp);
                } else {
                    array_push($erro, $linha);
                }
            }
            $numLinha++;
        }
        @fclose($handle);
    }

    return $dado;
}

switch ($stAcao) {
    case 'importar':
        if ( file_exists( $_FILES['arquivoColetora']['tmp_name'] ) ) {

            if ($_FILES['arquivoColetora']['type'] != 'text/plain') {
                $erro = new Erro();
                $erro->setDescricao( 'Formato invá¡lido do arquivo.' );
            } else {
                $timestamp = getdate();
                $dados = validarArquivo( $_FILES['arquivoColetora']['tmp_name'] );
                include_once(CAM_GP_PAT_NEGOCIO.'RPatrimonioArquivoColetora.class.php');
                $rule = new RPatrimonioArquivoColetora();
                $_FILES['arquivoColetora']['name'] = 'coleta_'.date('YmdHi', ($timestamp[0])).'.txt';
                $rule->nome = $_FILES['arquivoColetora']['name'];
                $rule->path = $_FILES['arquivoColetora']['tmp_name'];
                $rule->setMd5sum( $rule->path );

                $erro = $rule->importar($dados);
            }
        } else {
            $erro->setDescricao( "Não foi possível importar o arquivo! (".$_FILES['arquivoColetora']['name'].")." );
        }

        if ( !$erro->ocorreu() ) {
            SistemaLegado::alertaAviso( "FMImportarColetora.php?stAcao=importar&", "Arquivo ".$_FILES['arquivoColetora']['name']." importado com sucesso.",'importar','aviso', Sessao::getId(), '../' );
        } else {
            SistemaLegado::exibeAviso(urlencode($erro->getDescricao()), 'n_erro', 'erro', Sessao::getId(), '../');
            SistemaLegado::LiberaFrames();
        }
    break;
}
