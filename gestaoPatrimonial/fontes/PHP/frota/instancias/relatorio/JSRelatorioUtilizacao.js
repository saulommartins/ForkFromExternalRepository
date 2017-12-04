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
    * Data de Criação: 07/05/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * $Id: JSRelatorioUtilizacao.js 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.15
*/

?>
<script>

function verificaPeriodo(){
    var erro = false;
    var mensagem = "";
    
    if (document.getElementById('stPeriodoFinal')&&document.getElementById('stPeriodoInicial')) {	    
        var dataFinal = (document.getElementById('stPeriodoFinal').value).split("/");
        var dataInicial = (document.getElementById('stPeriodoInicial').value).split("/"); 
        if (dataFinal[2]&&dataInicial[2]) {
            var dtFinal = dataFinal[2];
            var dtInicial = dataInicial[2];
            
            if ( dtFinal != dtInicial ){
                erro = true;
                mensagem += "@Data Inicial e Data Final da Periodicidade devem ser do mesmo Ano!().";
                document.getElementById('stPeriodoFinal').value='';
            }
            
        }
    }
    
    if( erro ){ 
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}
</script>
