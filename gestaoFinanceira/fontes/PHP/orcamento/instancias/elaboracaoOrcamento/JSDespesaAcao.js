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
    * Arquivo javaScript para inclusão de despesa, agora utilizando ação
    * Data de Criação   : 12/08/2009


    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

    $Id:$
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
