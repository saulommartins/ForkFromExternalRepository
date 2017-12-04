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
    * Include para arquivo de Cadastro
    *
    *
    * @date: 09/08/2010
    * @author: Analista: Gelson
    * @author: Desenvol: Tonismar
    *
    * @ignore
**/

include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php");
$local = new TOrganogramaLocal();

$codigoLocais = ' where cod_local in ('.implode(',',$filtro['locaisSelecionados']).')';
$local->recuperaTodos($listaLocais,$codigoLocais);

$stColetoraDigitosLocal = sistemaLegado::pegaConfiguracao('coletora_digitos_local', 6, Sessao::getExercicio(), $boTransacao);
$pgFilt = CAM_GP_PAT_INSTANCIAS."inventario/FLExportarColetora.php";

if ( preg_match("/coletora_digitos_local/", $stColetoraDigitosLocal) ) {
    SistemaLegado::alertaAviso($pgFilt,"Necessário efetuar configuração da Coletora de Dados","n_incluir", "erro", Sessao::getId(), "../");
    exit();
}

$exportador->roUltimoArquivo->addBloco($listaLocais);

$exportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_local");
$exportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$exportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo($stColetoraDigitosLocal);

$exportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
$exportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$exportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
