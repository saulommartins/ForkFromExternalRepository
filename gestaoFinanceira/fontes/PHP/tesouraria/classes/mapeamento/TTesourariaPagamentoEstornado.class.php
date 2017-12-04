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
    * Classe de mapeamento da tabela TESOURARIA_PAGAMENTO_ESTORNADO
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTesourariaPagamentoEstornado.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-10-03 16:12:51 -0300 (Qua, 03 Out 2007) $

    * Casos de uso: uc-02.04.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_PAGAMENTO_ESTORNADO
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaPagamentoEstornado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaPagamentoEstornado()
{
    parent::Persistente();
    $this->setTabela("tesouraria.pagamento_estornado");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_nota,timestamp,timestamp_anulada');

    $this->AddCampo('exercicio'              , 'varchar'  , true , '04'  , true  , true  );
    $this->AddCampo('cod_entidade'           , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('cod_nota'               , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('timestamp'              , 'timestamp', true , ''    , true  , true  );
    $this->AddCampo('timestamp_anulado'      , 'timestamp', true , ''    , true  , true  );
    $this->AddCampo('exercicio_boletim'      , 'varchar'  , true , '04'  , false , true  );
    $this->AddCampo('cod_autenticacao'       , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'     , true , ''    , false , true  );
    $this->AddCampo('cod_boletim'            , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('cod_terminal'           , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('timestamp_terminal'     , 'timestamp', true , ''    , false , true  );
    $this->AddCampo('cgm_usuario'            , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('timestamp_usuario'      , 'timestamp', true , ''    , false , true  );
}
}
