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
    * Classe de mapeamento da tabela folhapagamento.ferias_evento
    * Data de Criação: 09/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.ferias_evento
  * Data de Criação: 09/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFeriasEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFeriasEvento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.ferias_evento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo,cod_evento,timestamp');

    $this->AddCampo('cod_tipo'  ,'integer'      ,true  ,'',true,'TFolhaPagamentoTipoEventoFerias');
    $this->AddCampo('cod_evento','integer'      ,true  ,'',true,'TFolhaPagamentoEvento');
    $this->AddCampo('timestamp' ,'timestamp_now',true  ,'',true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT ferias_evento.*                                          \n";
    $stSql .= "     , evento.codigo                                            \n";
    $stSql .= "     , evento.descricao                                         \n";
    $stSql .= "  FROM folhapagamento.ferias_evento                             \n";
    $stSql .= "     , (SELECT cod_tipo                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                      \n";
    $stSql .= "          FROM folhapagamento.ferias_evento                     \n";
    $stSql .= "        GROUP BY cod_tipo) as max_ferias_evento                 \n";
    $stSql .= "     , folhapagamento.evento                                    \n";
    $stSql .= " WHERE ferias_evento.cod_tipo = max_ferias_evento.cod_tipo      \n";
    $stSql .= "   AND ferias_evento.timestamp = max_ferias_evento.timestamp    \n";
    $stSql .= "   AND ferias_evento.cod_evento = evento.cod_evento             \n";

    return $stSql;
}

}
?>
