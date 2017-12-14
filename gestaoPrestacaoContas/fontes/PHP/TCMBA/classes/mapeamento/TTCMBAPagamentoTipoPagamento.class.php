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
/*
 * Classe de mapeamento da tabela tcmba.pagamento_tipo_documento_pagamento
 *
 * @package URBEM
 * @subpackage Mapeamento
 * @version $Id: TTCMBAPagamentoTipoPagamento.class.php 63464 2015-08-31 17:30:39Z michel $
 * @author Michel Teixeira
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAPagamentoTipoPagamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela("tcmba.pagamento_tipo_documento_pagamento");
    
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_nota,cod_entidade,timestamp,cod_tipo');
    
        $this->AddCampo('exercicio'             , 'varchar'  , true , '04'  , true  , true  );
        $this->AddCampo('cod_entidade'          , 'integer'  , true , ''    , true  , true  );
        $this->AddCampo('cod_nota'              , 'integer'  , true , ''    , true  , true  );
        $this->AddCampo('timestamp'             , 'timestamp', true , ''    , true  , true  );
        $this->AddCampo('cod_tipo'              , 'integer'  , true , ''    , true  , true  );
        $this->AddCampo('num_documento'         , 'varchar'  , true , '08'  , false , false );
    }
    
    public function __destruct() {}
}
