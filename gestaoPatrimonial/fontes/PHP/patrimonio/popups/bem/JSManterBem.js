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
<?
/**
    * Data de Criação: 13/09/2007

    
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura 
    
    * @package URBEM
    * @subpackage 
    
    $Revision: 27875 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-02-01 13:29:36 -0200 (Sex, 01 Fev 2008) $
    
    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/09/27 12:56:43  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório


*/
?>
<script type="text/javascript">

function insere(stCampoNum,stCampoNom,inNumCGM,stNomCGM){
    var x = "window.opener.parent.frames['telaPrincipal'].document.getElementById('"+stCampoNum+"').value = '"+inNumCGM+"';";
    var y = "window.opener.parent.frames['telaPrincipal'].document.getElementById('"+stCampoNum+"').focus();";
    var z = "window.opener.parent.frames['telaPrincipal'].document.getElementById('"+stCampoNom+"').innerHTML = '"+stNomCGM+"';";
    eval(x);
    eval(y);
    eval(z);
    //window.opener.parent.frames['telaPrincipal'].document.getElementById(stCampoNum).value = inNumCGM;
    //window.opener.parent.frames['telaPrincipal'].document.getElementById(stCampoNom).innerHTML = stNomCGM;
    window.close();
}
</script>

