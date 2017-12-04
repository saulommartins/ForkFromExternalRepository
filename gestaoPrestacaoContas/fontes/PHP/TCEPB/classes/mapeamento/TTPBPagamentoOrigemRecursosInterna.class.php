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
    * Classe de mapeamento da tabela TCEPB.PAGAMENTO_ORIGEM_RECURSOS_INTERNA
    * Data de Criação: 05/05/2014

    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TTPBPagamentoOrigemRecursosInterna.class.php 59708 2014-09-05 19:10:36Z michel $

    * Casos de uso:
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;


class TTPBPagamentoOrigemRecursosInterna  extends Persistente
{

  /**
      * Método Construtor
      * @access Private
  */
  public function TTPBPagamentoOrigemRecursosInterna()
  {
      parent::Persistente();
      $this->setTabela("tcepb.pagamento_origem_recursos_interna");
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
