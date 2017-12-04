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
    * Página JavaScript - Exportação Arquivos Principais

    * Data de Criação   : 31/01/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * @ignore

    $Revision: 62838 $
    $Name$
    $Autor: $
    $Date: 2015-06-26 10:02:49 -0300 (Fri, 26 Jun 2015) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.4  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">
function add_arquivos_extras(exercicio){ 
/**
* Function add_arquivos_extras 
* @ Desc    Inclui o arquivo RD_EXTRA.TXT e BVER_ENC.TXT
* no select de arquivos disponiveis para exportacao se o periodo selecionado for igual a 6
*/
        var inPer  = document.getElementById("inPeriodo")                                                 
        var selBim = inPer.options[inPer.selectedIndex].value;
        
        if (selBim == 6)
        {
            var rd_extra = new Option("RD_EXTRA.TXT","RD_EXTRA.TXT");
            var os       = document.getElementById("arCodArqDisponiveis").options;
            os[os.length] = rd_extra;

            if (exercicio >= 2015) {
                var bver_enc = new Option("BVER_ENC.TXT","BVER_ENC.TXT");
                var os       = document.getElementById("arCodArqDisponiveis").options;
                os[os.length] = bver_enc;                
            };                                     
        }                                                                                                         
        if (selBim < 6)
        {
                var x=document.getElementById("arCodArqDisponiveis")
                if(x.length == 9)
                {
                    x.options[8]=null
                }
        }
}

function preenchePeriodo(valor)
{  
    jq('select#inPeriodo').removeOption(/./);
    if(valor == 2){
        var arOption = {
                         '' : 'Selecione',
                         '1': '1º Bimestre',
                         '2': '2º Bimestre',
                         '3': '3º Bimestre',
                         '4': '4º Bimestre',
                         '5': '5º Bimestre',
                         '6': '6º Bimestre'
                       } 
    } else if(valor == 3) {
        var arOption = {
                         '' : 'Selecione',
                         '1': '1º Trimestre',
                         '2': '2º Trimestre',
                         '3': '3º Trimestre',
                         '4': '4º Trimestre',
                       }
                       
    } else if(valor == 4) {
        var arOption = {
                         '' : 'Selecione',
                         '1': '1º Quadrimestre',
                         '2': '2º Quadrimestre',
                         '3': '3º Quadrimestre',
                        } 
            
    } else {
        var arOption = {
                         '' : 'Selecione'
                       }
    } 
    jq('select#inPeriodo').addOption(arOption,false);
}

function Limpar(){
    limpaFormulario();
}
</script>
