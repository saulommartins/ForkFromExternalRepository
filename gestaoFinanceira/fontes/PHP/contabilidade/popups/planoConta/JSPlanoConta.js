<script type="text/javascript">
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
</script>
<?php
/**
    * Arquivo JavaScript
    * Data de Criação   : 05/11/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 30969 $
    $Name$
    $Autor:$
    $Date: 2007-05-18 18:05:53 -0300 (Sex, 18 Mai 2007) $

    * Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.6  2007/05/18 21:05:53  hboaventura
Arquivos para geração do TCMGO

Revision 1.5  2007/05/11 15:21:43  hboaventura
Arquivos para geração do TCEPB

Revision 1.4  2006/07/05 20:51:41  cleisson
Adicionada tag Log aos arquivos

*/
    
?>

<script type="text/javascript">

function insere(inCodigo,inEstrutural,stDescricao){
    if( eval( window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>') != null ) ) {
        window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = stDescricao;
        var campoHidden = eval( 'window.opener.parent.frames["telaPrincipal"].document.frm.<?=$_REQUEST["campoNom"]?>' );
        if( campoHidden != null ) campoHidden.value=stDescricao;
    }
    if( inCodigo != '' ){
        window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = inCodigo;
    }
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.close();
}
</script>
