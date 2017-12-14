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
    * Classe de mapeamento da tabela folhapagamento.beneficio_evento
    * Data de Criação: 27/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-19 11:18:17 -0300 (Ter, 19 Jun 2007) $

    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.beneficio_evento
  * Data de Criação: 27/06/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoBeneficioEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoBeneficioEvento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.beneficio_evento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao,timestamp,cod_tipo');

    $this->AddCampo('cod_configuracao'  ,'integer'      ,true   ,'' ,true   ,'TFolhaPagamentoConfiguracaoBeneficio');
    $this->AddCampo('timestamp'         ,'timestamp'    ,false  ,'' ,true   ,'TFolhaPagamentoConfiguracaoBeneficio');
    $this->AddCampo('cod_tipo'          ,'integer'      ,true   ,'' ,true   ,'TFolhaPagamentoTipoEventoBeneficio');
    $this->AddCampo('cod_evento'        ,'integer'      ,true   ,'' ,false  ,'TFolhaPagamentoEvento');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT beneficio_evento.*                                                           \n";
    $stSql .= "     , evento.codigo                                                                \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                          \n";
    $stSql .= "  FROM folhapagamento.beneficio_evento                                              \n";
    $stSql .= "     , (  SELECT cod_configuracao                                                   \n";
    $stSql .= "               , max(timestamp) as timestamp                                        \n";
    $stSql .= "            FROM folhapagamento.configuracao_beneficio                              \n";
    $stSql .= "        GROUP BY cod_configuracao) as configuracao_beneficio                        \n";
    $stSql .= "     , folhapagamento.evento                                                        \n";
    $stSql .= " WHERE beneficio_evento.cod_configuracao = configuracao_beneficio.cod_configuracao  \n";
    $stSql .= "   AND beneficio_evento.timestamp = configuracao_beneficio.timestamp                \n";
    $stSql .= "   AND beneficio_evento.cod_evento = evento.cod_evento                              \n";

    return $stSql;
}

}
