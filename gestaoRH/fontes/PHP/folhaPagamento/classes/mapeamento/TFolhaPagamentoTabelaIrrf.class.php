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
/**
    * Classe de mapeamento da tabela folhapagamento.tabela_irrf
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.tabela_irrf
  * Data de Criação: 05/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoTabelaIrrf extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoTabelaIrrf()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.tabela_irrf");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tabela,timestamp');

    $this->AddCampo('cod_tabela','integer',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('vl_dependente','numeric',true,'14,2',false,false);
    $this->AddCampo('vl_limite_isencao','numeric',true,'14,2',false,false);
    $this->AddCampo('vigencia','date',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT tabela_irrf.cod_tabela                                \n";
    $stSql .= "     , tabela_irrf.vl_dependente                             \n";
    $stSql .= "     , tabela_irrf.vl_limite_isencao                         \n";
    $stSql .= "     , tabela_irrf.timestamp                                 \n";
    $stSql .= "     , to_char(tabela_irrf.vigencia,'dd/mm/yyyy') as vigencia\n";
    $stSql .= "  FROM folhapagamento.tabela_irrf                            \n";
    $stSql .= "     , (  SELECT cod_tabela                                  \n";
    $stSql .= "               , vigencia                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                 \n";
    $stSql .= "            FROM folhapagamento.tabela_irrf                  \n";
    $stSql .= "        GROUP BY cod_tabela                                  \n";
    $stSql .= "               , vigencia) as max_tabela_irrf                \n";
    $stSql .= " WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela   \n";
    $stSql .= "   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp    \n";
    $stSql .= "   AND tabela_irrf.vigencia  = max_tabela_irrf.vigencia      \n";

    return $stSql;
}

function recuperaUltimaVigencia(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaUltimaVigencia",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaUltimaVigencia()
{
    $stSql = "select max(vigencia) as vigencia from folhapagamento.tabela_irrf";

    return $stSql;
}

}
