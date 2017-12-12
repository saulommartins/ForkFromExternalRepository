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
 * Data de Criação: 28/09/2006
 
 
 * @author Analista: Cleisson Barboza
 * @author Desenvolvedor: Anderson C. Konze
 
 * @ignore
 
 * Casos de uso: uc-03.04.05
 
 $Id: JSManterMapaCompras.js 59612 2014-09-02 12:00:51Z gelson $
 
 */

?>

<script type="text/javascript">

   function formataUS( valor ) {
      var retorno = valor;
   
      retorno = valor.replace( new RegExp( "[\.]", "gi" ), ""   );
      retorno = retorno.replace( new RegExp( ",","gi" )    , "."  );
   
      return retorno;
   }

   function limparDadosSolicitacao(){
      
      if ( document.getElementById('HdnTotalEntidade').value > 0) {
         if( document.getElementById('stNomSolicitacao') )
            document.getElementById('stNomSolicitacao').innerHTML = '&nbsp;';
         
         if( document.frm.inCodSolicitacao )
            document.frm.inCodSolicitacao.value = '';        
   
         if ( document.getElementById('HdnTotalEntidade').value > 1 ) {      
            if( document.frm.inCodEntidadeSolicitacao )
               document.frm.inCodEntidadeSolicitacao.value='';
             
            if( document.frm.stNomEntidadeSolicitacao )
               document.frm.stNomEntidadeSolicitacao.value='';       
         }
      }
   }

</script>