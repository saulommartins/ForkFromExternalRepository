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
    * JavaScript para o validação do Formulario de Inclusao/Alteracao de Fiscal

    * Data de Criação   : 01/08/2007


    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

	* $Id: JSManterFiscal.js 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/
?>
<script>
    function incluirFiscalizacao(){
       if(document.frm.inTipoFiscalizacao.value!=""){
              montaParametrosGET( 'incluirAtributoFiscal', '', true); 
       }else{
              alertaAviso('Campo Tipo de Fiscalização inválido!()','form','erro','<?=Sessao::getId()?>');
       }
    } 

    function Limpar(){
        document.frm.inTipoFiscalizacao.value                   = "";
        document.getElementById('stTipoFiscalizacao').innerHTML = "&nbsp";
    }

    function Cancelar(){
        <?
            $link = Sessao::read( "link" );
            $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
        ?>
        document.frm.target = "telaPrincipal";
        document.frm.action = "<?=$pgList.'?'.Sessao::getId();?>";
        document.frm.submit();
    }
</script>
