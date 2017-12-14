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
    * Classe de mapeamento da tabela TESOURARIA.PAGAMENTO_ORIGEM_RECURSOS_INTERNA
    * Data de Criação: 14/05/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TTesourariaPagamentoOrigemRecursosInterna.class.php 59671 2014-09-04 15:00:28Z michel $

    * Casos de uso: uc-02.04.05
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TESOURARIA.PAGAMENTO_ORIGEM_RECURSOS_INTERNA
  * Data de Criação: 14/05/2008

  * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaPagamentoOrigemRecursosInterna  extends Persistente
{

  /**
      * Método Construtor
      * @access Private
  */
  public function TTesourariaPagamentoOrigemRecursosInterna()
  {
      parent::Persistente();
      $this->setTabela("tesouraria.pagamento_origem_recursos_interna ");
      $this->setCampoCod('');
      $this->setComplementoChave('cod_entidade, exercicio, cod_nota, cod_origem_recursos');
      $this->AddCampo('cod_entidade'            , 'integer'  , true, '' , true, true );
      $this->AddCampo('exercicio'               , 'char'     , true, '4', true, true );
      $this->AddCampo('cod_nota'                , 'integer'  , true, '' , true, true );
      $this->AddCampo('timestamp'               , 'timestamp', true, '' , true, true );
      $this->AddCampo('cod_origem_recursos'     , 'integer'  , true, '' , true, true );
      $this->AddCampo('exercicio_origem_recurso', 'char'     , true, '4', false, true);
  }

}
