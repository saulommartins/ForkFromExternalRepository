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
* Página de Formulario de Seleção de Impressora para Relatorio
* Data de Criação   : 13/04/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage

$Revision: 30547 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.04
*/

include_once '../../../bibliotecas/funcoes.lib.php';
include_once '../../../includes/Constante.inc.php';
include_once( CAM_INCLUDES."IncludeClasses.inc.php" );
include_once( CAM_GRH_CON_NEGOCIO."RRelatorio.class.php" );
include_once( CAM_GRH_CON_NEGOCIO."RDocumentoDinamicoDocumento.class.php" );

$obPDF = new DocumentoPDF;
$obRRelatorio = new RRelatorio;
$rsRecordSetCargo = $rsRecordSet = $rsDocumento = $rsBlocoTexto = new RecordSet;

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;
$obRDocumentoDinamico->setCodDocumento($sessao->transf1);
$obRDocumentoDinamico->consultarDocumento($rsDocumento,$boTransacao);
$obRDocumentoDinamico->listarDocumentoBlocoTexto($arBlocoTexto,$stOrder,$boTransacao);

$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$stTitulo        = $rsDocumento->getCampo('titulo');
$inMargem_dir    = $rsDocumento->getCampo('margem_dir');
$inMargem_esq    = $rsDocumento->getCampo('margem_esq');
$inMargem_sup    = $rsDocumento->getCampo('margem_sup');
$stFonte         = $rsDocumento->getCampo('fonte');
$inTamanhoFonte  = $rsDocumento->getCampo('tamanho_fonte');

$rsRecordSet = $sessao->transf2;
$rsRecordSetCargo = $sessao->transf3;

// mostra a data com o mes em portugues
$mes = array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
$data = date("j")." de ".$mes[date("n")]." de ".date("Y");

$dataExtenso = ucfirst(strtolower(DIAEXTENSO(date("d/m/Y"))));

$arVT    = array();
$inCount = 0;

while ( !$rsRecordSet->eof() ) {

    $arVT[$inCount]['dataExtenso'] = $dataExtenso;
    $arVT[$inCount]['data']	=	$data;
    $arVT[$inCount]['nom_cgm']        = trim($rsRecordSet->getCampo('nom_cgm'));
    $arVT[$inCount]['numcgm']        = $rsRecordSet->getCampo('numcgm');
    $arVT[$inCount]['classificacao']        = $rsRecordSet->getCampo('classificacao');
    $arVT[$inCount]['nota_prova']        = $rsRecordSet->getCampo('nota_prova');
    $arVT[$inCount]['logradouro']        = $rsRecordSet->getCampo('logradouro');
    $arVT[$inCount]['numero']        = $rsRecordSet->getCampo('numero');
    $arVT[$inCount]['complemento']        = $rsRecordSet->getCampo('complemento');
    $arVT[$inCount]['bairro']        = $rsRecordSet->getCampo('bairro');
    $arVT[$inCount]['cep']        = $rsRecordSet->getCampo('cep');
    $arVT[$inCount]['nom_municipio']        = $rsRecordSet->getCampo('nom_municipio');
    $arVT[$inCount]['sigla_uf']        = $rsRecordSet->getCampo('sigla_uf');
    $arVT[$inCount]['fone_residencial']        = $rsRecordSet->getCampo('fone_residencial');
    $arVT[$inCount]['fone_celular']        = $rsRecordSet->getCampo('fone_celular');

    $rsRecordSetCargo->setPrimeiroElemento();

    while (!$rsRecordSetCargo->eof() ) {
        if ($rsRecordSetCargo->getCampo('cod_candidato')==$rsRecordSet->getCampo('cod_candidato')) {
            $arVT[$inCount]['descricao'] = strtoupper($rsRecordSetCargo->getCampo('descricao'));
        }
        $rsRecordSetCargo->proximo();
    }
    $inCount++;
    $rsRecordSet->proximo();
}

$rsVT = new RecordSet;
$rsVT->preenche( $arVT );

$obPDF->addRecordSet ($rsVT);

$obPDF->addTitulo ($stTitulo, "times","", 18);
$obPDF->setAlturaLinha (6);
$obPDF->setMargens ($inMargem_esq,$inMargem_sup,$inMargem_dir);
$obPDF->setFonte ("times", $inTamanhoFonte);

foreach ($arBlocoTexto as $obBlocoTexto) {
    $stTexto       = $obBlocoTexto->getTexto();
    $stAlinhamento = $obBlocoTexto->getAlinhamento();
    $obPDF->addTexto ($stTexto,$stAlinhamento );
    $obPDF->setCampo("nom_cgm");
    $obPDF->setCampo("numcgm");
    $obPDF->setCampo("classificacao");
    $obPDF->setCampo("nota_prova");
    $obPDF->setCampo("logradouro");
    $obPDF->setCampo("numero");
    $obPDF->setCampo("complemento");
    $obPDF->setCampo("bairro");
    $obPDF->setCampo("cep");
    $obPDF->setCampo("nom_municipio");
    $obPDF->setCampo("sigla_uf");
    $obPDF->setCampo("fone_residencial");
    $obPDF->setCampo("fone_celular");
    $obPDF->setCampo("nom_cgm");
    $obPDF->setCampo("descricao");
    $obPDF->setCampo("data");
    $obPDF->setCampo("dataExtenso");
}

$obPDF->show();
?>
