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
    

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-09-12 07:03:07 -0300 (Ter, 12 Set 2006) $
    
    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.5  2006/09/12 10:03:07  jose.eduardo
Bug #6964#

Revision 1.4  2006/08/18 19:32:56  eduardo
Bug #5238#
Bug #5239#

Revision 1.3  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function limpaForm( BuscaValor ){
    document.frm.reset();
    document.frm.inCodOrgao.value = '';
    limpaSelect(document.frm.inCodUnidade,0);
    document.frm.inCodOrgao.options[0].selected = true;
    document.frm.inCodUnidade.options[0] = new Option('Selecione','', 'selected');
    document.getElementById('stDescricaoDespesa').innerHTML = '&nbsp;';    
    document.getElementById('stDescricaoRecurso').innerHTML = '&nbsp;';        
}

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}

function formataUS( valor ) {
    var retorno = valor;
    
    retorno = valor.replace( new RegExp( "[\.]", "gi" ), ""   );
    retorno = retorno.replace( new RegExp( ",","gi" )    , "."  );
    
    return retorno;
}

</script>
