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
    * Classe de mapeamento da tabela folhapagamento.pensao_evento
    * Data de Criação: 26/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2007-11-05 15:28:18 -0200 (Seg, 05 Nov 2007) $

    * Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.pensao_evento
  * Data de Criação: 26/05/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPensaoEvento extends Persistente
{
   /**
       * Método Construtor
       * @access Private
   */
   public function TFolhaPagamentoPensaoEvento()
   {
       parent::Persistente();
       $this->setTabela("folhapagamento.pensao_evento");

       $this->setCampoCod('');
       $this->setComplementoChave('timestamp,cod_configuracao_pensao,cod_tipo');

       $this->AddCampo('timestamp','timestamp',false,'',true, 'TFolhaPagamentoPensaoFuncaoPadrao' );
       $this->AddCampo('cod_configuracao_pensao','integer',true,'',true, 'TFolhaPagamentoPensaoFuncaoPadrao' );

       $this->AddCampo('cod_tipo','integer',true,'',true, 'TFolhaPagamentoTipoEventoPensao' );

       $this->AddCampo('cod_evento','integer',true,'',false, 'TFolhaPagamentoEvento' );

   }

   public function recuperaPensaoEventoEventos(&$rsRecordSet, $stFiltro = '', $stOrdem = '')
   {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaPensaoEventoEventos().$stFiltro.$stOrdem;

       $this->stDebug = $stSql;
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

       return $obErro;
   }

   public function montaRecuperaPensaoEventoEventos()
   {
       $stSql = '';

       $stSql .= "select pensao_evento.cod_tipo                                                                \n ";
       $stSql .= ",trim(evento.descricao) as descricao                                                                            \n ";
       $stSql .= ",evento.codigo                                                                               \n ";
       $stSql .= ",evento.cod_evento                                                                           \n ";
       $stSql .= "from folhapagamento.pensao_evento                                                            \n ";
       $stSql .= "inner join folhapagamento.evento on (pensao_evento.cod_evento = evento.cod_evento)           \n ";
       $stSql .= "inner join                                                                                   \n ";
       $stSql .= "   ( select cod_configuracao_pensao, max( timestamp) as max_timestamp                        \n ";
       $stSql .= "     from folhapagamento.pensao_evento                                                       \n ";
       $stSql .= "     group by cod_configuracao_pensao ) as max_pensao_evento                                 \n ";
       $stSql .= "   on ( max_pensao_evento.cod_configuracao_pensao = pensao_evento.cod_configuracao_pensao    \n ";
       $stSql .= "    and max_pensao_evento.max_timestamp          = pensao_evento.timestamp )                 \n ";

       return $stSql ;

   }

}
