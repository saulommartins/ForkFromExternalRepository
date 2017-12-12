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
    * Classe de mapeamento da tabela licitacao.homologacao_anulada
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18353 $
    $Name$
    $Author: andre.almeida $
    $Date: 2006-11-29 13:46:00 -0200 (Qua, 29 Nov 2006) $

    * Casos de uso: uc-03.05.21
*/
/*
$Log$
Revision 1.4  2006/11/29 15:46:00  andre.almeida
Atualizado

Revision 1.3  2006/11/27 11:08:06  andre.almeida
Atualizado

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.homologacao_anulada
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoHomologacaoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoHomologacaoAnulada()
{
    parent::Persistente();
    $this->setTabela("licitacao.homologacao_anulada");

    $this->setCampoCod('');
    $this->setComplementoChave('num_homologacao, num_adjudicacao, cod_entidade, cod_modalidade, cod_licitacao, exercicio_licitacao, cod_item, cod_cotacao, lote, exercicio_cotacao, cgm_fornecedor');

    $this->AddCampo( 'num_homologacao'      , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'num_adjudicacao'      , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cod_entidade'         , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cod_modalidade'       , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cod_licitacao'        , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'exercicio_licitacao'  , 'char'         , true ,'4', true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cod_item'             , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cgm_fornecedor'       , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'cod_cotacao'          , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'lote'                 , 'integer'      , true ,'' , true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'exercicio_cotacao'    , 'char'         , true ,'4', true , 'TLicitacaoHomologacao' );
    $this->AddCampo( 'timestamp'            , 'timestamp_now', false,'' , true , false );
    $this->AddCampo( 'motivo'               , 'text'         , true ,'' , false, false );
    $this->AddCampo( 'revogacao'            , 'boolean'      , false,'' , false, false );
}
}
