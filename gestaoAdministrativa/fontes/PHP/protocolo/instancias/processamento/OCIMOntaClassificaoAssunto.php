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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Id: OCIMOntaClassificaoAssunto.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stMascaraAssunto    = SistemaLegado::pegaConfiguracao('mascara_assunto', 5, Sessao::getExercicio() );
$arMascara           = preg_split( "/[^0-9a-zA-Z]/", $stMascaraAssunto);
$stMascClassificacao = $arMascara[0];
$stMascAsssunto      = $arMascara[1];
$stSeparador         = trim( preg_replace( "/[0-9a-zA-Z]/", "", $stMascaraAssunto ) );

switch ($_GET['stCtrl']) {
    case "classificacao":
        $codClassificacao = $_GET['codClassificacao'];
    case "chave":
        $arChave = preg_split( "/[^0-9a-zA-Z]/", $_GET['codClassifAssunto'] );
        $codClassificacao = isset($codClassificacao) ? $codClassificacao : (integer) $arChave[0];
        $codClassificacao = $codClassificacao < 1 ? "" : $codClassificacao;
        $codAssunto = (integer) $arChave[1];
        echo "limpaSelect( document.frm.codAssunto,1);\n";
        echo "document.frm.codClassificacao.value= '".$codClassificacao."';\n";
        if ($codClassificacao > 0) {
            include_once( CAM_GA_PROT_MAPEAMENTO."TAssunto.class.php" );
            $obTAssunto = new TAssunto;
            $stFiltro = " where cod_classificacao = ".$codClassificacao;
            $obTAssunto->recuperaTodos( $rsAssunto, $stFiltro, "nom_assunto" );
            $inContador = 0;
            while ( !$rsAssunto->eof() ) {
                $stAssunto = addslashes($rsAssunto->getCampo("nom_assunto"));
                $inAssunto = $rsAssunto->getCampo("cod_assunto");
                echo "document.frm.codAssunto.options[".++$inContador."] = new Option('".$stAssunto."',".$inAssunto.");\n";
                $rsAssunto->proximo();
            }
            if ($codAssunto > 0) {
                echo "document.frm.codAssunto.value = ".$codAssunto.";\n";
            }
        }
    break;
    case "assunto":
    break;
}

if (!empty($_REQUEST['codClassifAssunto'])) {
    echo "document.frm.codClassifAssunto.value='".$_REQUEST['codClassifAssunto']."';\n";
} else {
    $stAssuntoMascarado = str_pad($_REQUEST["codClassificacao"], strlen( $stMascClassificacao ), "0", STR_PAD_LEFT);
    $stAssuntoMascarado .= $stSeparador;
    $stAssuntoMascarado .= str_pad($_REQUEST["codAssunto"], strlen( $stMascAsssunto ), "0", STR_PAD_LEFT);
    echo "document.frm.codClassifAssunto.value='".$stAssuntoMascarado."';\n";
}

?>
