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
    * Classe de mapeamento da tabela licitacao.adjudicacao_anulada
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18353 $
    $Name$
    $Author: andre.almeida $
    $Date: 2006-11-29 13:46:00 -0200 (Qua, 29 Nov 2006) $

    * Casos de uso: uc-03.05.20
*/
/*
$Log$
Revision 1.5  2006/11/29 15:46:00  andre.almeida
Atualizado

Revision 1.4  2006/11/29 14:56:39  andre.almeida
Atualizado

Revision 1.3  2006/11/24 17:00:47  andre.almeida
Alterado os campos.

Revision 1.2  2006/11/08 10:51:41  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.adjudicacao_anulada
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoAdjudicacaoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoAdjudicacaoAnulada()
{
    parent::Persistente();
    $this->setTabela("licitacao.adjudicacao_anulada");

    $this->setCampoCod('');
    $this->setComplementoChave('num_adjudicacao, cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao, lote, cod_cotacao, cod_item, exercicio_cotacao, cgm_fornecedor');

    $this->AddCampo( 'num_adjudicacao'      , 'integer'      , true , ''  , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cod_licitacao'        , 'integer'      , true , ''  , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cod_modalidade'       , 'integer'      , true , ''  , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cod_entidade'         , 'integer'      , true , ''  , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'exercicio_licitacao'  , 'char'         , true , '4' , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'lote'                 , 'integer'      , true , ''  , false, 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cod_cotacao'          , 'integer'      , true , ''  , false, 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cgm_fornecedor'       , 'integer'      , true , ''  , false, 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'cod_item'             , 'integer'      , true , ''  , true , 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'exercicio_cotacao'    , 'char'         , true , '4' , false, 'TLicitacaoAdjudicacao' );
    $this->AddCampo( 'motivo'               , 'text'         , false, ''  , false, false );
    $this->AddCampo( 'timestamp'            , 'timestamp_now', false, ''  , true , false );
}
}
