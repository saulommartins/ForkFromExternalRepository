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
    * Classe de mapeamento da tabela folhapagamento.decimo_evento
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.55
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.decimo_evento
  * Data de Criação: 03/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoDecimoEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoDecimoEvento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.decimo_evento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo,cod_evento,timestamp');

    $this->AddCampo('cod_tipo'  ,'integer'      ,false ,'',true,'TFolhaPagamentoTipoEventoDecimo');
    $this->AddCampo('cod_evento','integer'      ,false ,'',true,'TFolhaPagamentoEvento');
    $this->AddCampo('timestamp' ,'timestamp_now',true  ,'',true,false);

}

function recuperaDecimoEventoEventos(&$rsRecordSet, $stFiltro = '', $stOrdem = '')
{
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaDecimoEventoEventos().$stFiltro.$stOrdem;

       $this->stDebug = $stSql;
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

       return $obErro;
   }
   public function montaRecuperaDecimoEventoEventos()
   {
        $stSql .= "SELECT decimo_evento.cod_tipo                                                                 \n ";
        $stSql .= ",trim(evento.descricao) as descricao                                                          \n ";
        $stSql .= ",evento.codigo                                                                                \n ";
        $stSql .= "from folhapagamento.decimo_evento                                                             \n ";
        $stSql .= "inner join folhapagamento.evento on (decimo_evento.cod_evento = evento.cod_evento)            \n ";
        $stSql .= "inner join                                                                                    \n ";
        $stSql .= "   ( select max( timestamp) as timestamp                                                      \n ";
        $stSql .= "     from folhapagamento.decimo_evento) as max_timestamp                                      \n ";
        $stSql .= "   on ( max_timestamp.timestamp = decimo_evento.timestamp  )                                  \n ";

        return $stSql ;
   }
}
?>
