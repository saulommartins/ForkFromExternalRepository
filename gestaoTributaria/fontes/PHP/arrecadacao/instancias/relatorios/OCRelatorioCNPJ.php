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
    * Frame Oculto para relatorio de CNPJ's
    * Data de Criação:

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Vitor Hugo

    * @ignore

    * $Id: OCRelatorioCNPJ.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    require_once(CAM_GT_ARR_MAPEAMENTO.'FARRListaRelatorioCNPJ.class.php');

    $obListaEmissao = new FARRListaRelatorioCNPJ;

    $obErro = $obListaEmissao->executaFuncao ( $rsListaCNPJ );
    if ( $rsListaCNPJ->getNumLinhas() < 1 ) {
       $obErro->setDescricao ('Nenhum registro foi encontrado!');
       sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro");
       exit;
    } else {
       $arX = $rsListaCNPJ->arElementos;
    }


    if ( !$obErro->ocorreu() ) {
       $nome_arquivo  = "cnpj_".date("Y-m-d").'_'.date("h-i-s");
       $nome_arquivo  = "/tmp/".$nome_arquivo.".txt";
       $arquivo = fopen ( $nome_arquivo, "w+" );

       $rsListaCNPJ->setPrimeiroElemento();
       while ( !$rsListaCNPJ->eof()  ) {
           fwrite( $arquivo,  $rsListaCNPJ->getCampo("cnpj")."\n"  );
           $rsListaCNPJ->proximo();
       }

       fclose ( $arquivo );

       $pgTMP = "FLRelatorioCNPJ.php?stNomeArquivo=".$nome_arquivo;

       $file = $nome_arquivo;

       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header('Content-Description: File Transfer');
       header('Content-Type: application/force-download');
       header('Content-Length: ' . filesize($file));
       header('Content-Disposition: attachment; filename=Anexo'.substr(basename($file),strrpos(basename($file),'.'),strlen(basename($file))) );
       readfile($file);

//       SistemaLegado::alertaAviso($pgTMP."?stAcao=emitir","Arquivo CNPJ emitido com sucesso!","incluir","aviso", Sessao::getId(), "../");
    }
?>
