<?php
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
?>
<?php
/*
 * Arquivo de mapeamento da tabela tcepb.configurar_ide
 * Data de Criação   : 07/01/2014

 * @author Analista      Eduardo Paculski Schitz
 * @author Desenvolvedor Franver Sarmento de Moraes

 * @package URBEM
 * @subpackage

 * @ignore

  $Id: TTCEMGConfiguracaoPERC.class.php 59719 2014-09-08 15:00:53Z franver $
  $Date: 2014-09-08 12:00:53 -0300 (Mon, 08 Sep 2014) $
  $Author: franver $
  $Rev: 59719 $

 */

class TTCEMGConfiguracaoPERC extends Persistente
{
    public function TTCEMGConfiguracaoPERC()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_perc');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');
        $this->AddCampo('exercicio'         ,'char'    ,true,'4', true,false);
        $this->AddCampo('planejamento_anual','integer' ,true,'' ,false,false);
        $this->AddCampo('porcentual_anual'  ,'numeric' ,true,'' ,false,false);
    }

    public function recuperaExportacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaExportacao()
    {
        $stSql = "SELECT
                            configuracao_perc.planejamento_anual AS planejamento_anual
                          , REPLACE(configuracao_perc.porcentual_anual::VARCHAR,'.',',') AS porcentual_anual
                    FROM tcemg.configuracao_perc

                   WHERE configuracao_perc.exercicio = '".Sessao::getExercicio()."'";

        return $stSql;
    }
    
    public function __destruct(){}

}
?>
