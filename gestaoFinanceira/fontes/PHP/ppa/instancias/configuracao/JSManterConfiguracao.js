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
    * Data de Criação   : 26/10/2005


    * @author Analista: Anelise 
    * @author Desenvolvedor: Leandro Zis

    * @ignore
    
    * Casos de uso: uc-02.09.01

*/

/*
$Log$
Revision 1.1  2007/06/05 14:37:35  leandro.zis
uc 02.09.01


*/
?>
<script type="text/javascript">

function  verificaProtocolo( nroProtocolo ){
    if (nroProtocolo.value.length < 9) {
       return false
    }
    else {
       if ( nroProtocolo.value.length < 11 ){
          if( nroProtocolo.value.substr(7,2) > 80 ) {
             nroProtocolo.value  = nroProtocolo.value.substr(0,7) + '19' + nroProtocolo.value.substr(7,2);
          }
          else {
             nroProtocolo.value  = nroProtocolo.value.substr(0,7) + '20' + nroProtocolo.value.substr(7,2);
          }
       }
       return true;
    }
}

function limpaFormularioExtra() {
   document.frm.inCodNormaAnterior.valeu = undefined;
}

</script>
                
