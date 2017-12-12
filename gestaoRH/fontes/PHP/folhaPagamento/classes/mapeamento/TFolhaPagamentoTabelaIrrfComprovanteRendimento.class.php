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
    * Classe de mapeamento da tabela folhapagamento.tabela_irrf_comprovante_rendimento
    * Data de Criação: 19/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TFolhaPagamentoTabelaIrrfComprovanteRendimento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.05.37
*/
/*
$Log: TFolhaPagamentoTabelaComprovanteRendimento.class.php,v $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.tabela_irrf_comprovante_rendimento
  * Data de Criação: 19/10/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Alex Cardoso

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoTabelaIrrfComprovanteRendimento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoTabelaIrrfComprovanteRendimento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.tabela_irrf_comprovante_rendimento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tabela,timestamp,cod_evento');

    $this->AddCampo('cod_tabela','integer'  ,true  ,'',true,'TFolhaPagamentoTabelaIrrf');
    $this->AddCampo('timestamp' ,'timestamp',true  ,'',true,'TFolhaPagamentoTabelaIrrf');
    $this->AddCampo('cod_evento','integer'  ,true  ,'',true,'TFolhaPagamentoEvento');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT tabela_irrf_comprovante_rendimento.*                                  \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                      \n";
    $stSql .= "     , evento.codigo                                         \n";
    $stSql .= "     , evento.natureza                                       \n";
    $stSql .= "  FROM folhapagamento.tabela_irrf_comprovante_rendimento                     \n";
    $stSql .= "     , (SELECT cod_tabela                                    \n";
    $stSql .= "             , max(timestamp) as timestamp                   \n";
    $stSql .= "          FROM folhapagamento.tabela_irrf_comprovante_rendimento             \n";
    $stSql .= "        GROUP BY cod_tabela) as max_tabela_irrf_comprovante_rendimento       \n";
    $stSql .= "     , folhapagamento.evento                                 \n";
    $stSql .= " WHERE tabela_irrf_comprovante_rendimento.cod_evento = evento.cod_evento     \n";
    $stSql .= "   AND tabela_irrf_comprovante_rendimento.cod_tabela = max_tabela_irrf_comprovante_rendimento.cod_tabela \n";
    $stSql .= "   AND tabela_irrf_comprovante_rendimento.timestamp  = max_tabela_irrf_comprovante_rendimento.timestamp  \n";

    return $stSql;
}

}
?>
