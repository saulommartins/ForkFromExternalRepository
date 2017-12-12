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
    * JavaScript de Relatório Configurável de Evento
    * Data de Criação: 13/04/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.51
*/

/*
$Log$
Revision 1.3  2006/08/08 17:43:36  vandre
Adicionada tag log.

*/
?>

<script>

function validaSelectMultiplo(){
    var valida = true;
    jQuery("select[name$='Selecionados[]']").each(function(){        
        if (this.length == 0) {            
            valida = false;
        }else{
            for(var inCount=0; inCount < this.length; inCount++){
                this.options[ inCount ].selected = true;
            }    
        }
    });
    return valida;
}
</script>
