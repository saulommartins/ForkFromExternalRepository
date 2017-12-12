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
    * Classe de mapeamento da tabela licitacao.cotacao_licitacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19325 $
    $Name$
    $Author: domluc $
    $Date: 2007-01-15 10:36:27 -0200 (Seg, 15 Jan 2007) $

    * Casos de uso: uc-03.05.25
*/
/*
$Log$
Revision 1.4  2007/01/15 12:36:27  domluc
Correção para ficar de acordo com a base de dados.

Revision 1.3  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.2  2006/09/22 14:41:47  leandro.zis
ajustes devido a alteração da coluna 'exercicio_entidade' da tabela licitacao.licitacao para exercicio

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.cotacao_licitacao
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoCotacaoLicitacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TLicitacaoCotacaoLicitacao()
    {
        parent::Persistente();
        $this->setTabela("licitacao.cotacao_licitacao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao, lote, cod_cotacao, cgm_fornecedor, cod_item, exercicio_cotacao');

        $this->AddCampo('cod_licitacao'     ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
        $this->AddCampo('cod_modalidade'    ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
        $this->AddCampo('cod_entidade'      ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
        $this->AddCampo('exercicio_licitacao','char'   ,false ,'4'  ,true,'TLicitacaoLicitacao','exercicio');
        $this->AddCampo('lote'       		,'integer',false ,''   ,true,'TComprasCotacaoFornecedorItem');
        $this->AddCampo('cod_cotacao'       ,'integer',false ,''   ,true,'TComprasCotacaoFornecedorItem');
        $this->AddCampo('cgm_fornecedor'    ,'integer',false ,''   ,true,'TComprasCotacaoFornecedorItem');
        $this->AddCampo('cod_item'    		,'integer',false ,''   ,true,'TComprasCotacaoFornecedorItem');
        $this->AddCampo('exercicio_cotacao' ,'char'   ,false ,'4'  ,true,'TComprasCotacaoFornecedorItem', 'exercicio');
    }
}
