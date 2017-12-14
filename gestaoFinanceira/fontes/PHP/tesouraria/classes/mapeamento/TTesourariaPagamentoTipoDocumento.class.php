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
    * Classe de mapeamento da tabela TESOURARIA.PAGAMENTO_TIPO_DOCUMENTO
    * Data de Criação: 12/01/2009

    * @author Analista: Tonismar Régis
    * @author Desenvolvedor: Lucas Andrades Mendes

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA.PAGAMENTO_TIPO_DOCUMENTO
  * Data de Criação: 01/12/2009

  * @author Analista: Tonismar Régis
  * @author Desenvolvedor: Lucas Andrades Mendes

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaPagamentoTipoDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaPagamentoTipoDocumento()
{
    parent::Persistente();
    $this->setTabela("tesouraria.pagamento_tipo_documento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,exercicio,cod_nota,cod_tipo_documento');

    $this->AddCampo('cod_tipo_documento'        , 'integer'  , true, ''  , true  ,true );
    $this->AddCampo('cod_entidade'              , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio'                 , 'varchar'  , true, '04', true  , true  );
    $this->AddCampo('timestamp'                 , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cod_nota'                  , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('num_documento'             , 'varchar'  , true, '15', false , true  );
}
}
