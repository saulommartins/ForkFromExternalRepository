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
    * Classe de mapeamento da tabela TESOURARIA_ARRECADACAO_ESTORNADA
    * Data de Criação: 07/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-09-05 08:22:10 -0300 (Ter, 05 Set 2006) $

    * Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.12  2006/09/05 11:22:10  jose.eduardo
Bug #6759#

Revision 1.11  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ARRECADACAO_ESTORNADA
  * Data de Criação: 10/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaArrecadacaoEstornada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaArrecadacaoEstornada()
{
    parent::Persistente();
    $this->setTabela("tesouraria.arrecadacao_estornada");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_arrecadacao,exercicio,timestamp_arrecadacao,timestamp_estornada');

    $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio'             , 'char'     , true, '4' , true  , true  );
    $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true, ''  , true  , true  );
    $this->AddCampo('timestamp_estornada'   , 'timestamp', true, ''  , true  , false );
    $this->AddCampo('cod_autenticacao'       , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'     , true , ''  , false , true  );
    $this->AddCampo('cod_terminal'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal'    , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'           , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'     , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('observacao'            , 'text'     , true, ''  , false , false );
    $this->AddCampo('cod_entidade'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cod_boletim'           , 'integer'  , true, ''  , false , true  );
}

}
