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
    * Classe de mapeamento da tabela TESOURARIA_ARRECADACAO_RECEITA_DEDUTORA_ESTORNADA
    * Data de Criação: 30/08/2006

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-09-01 13:56:54 -0300 (Sex, 01 Set 2006) $

    * Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.1  2006/09/01 16:56:14  jose.eduardo
uc-02.04.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ARRECADACAO_RECEITA_DEDUTORA_ESTORNADA
  * Data de Criação: 30/08/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Jose Eduardo Porto

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaArrecadacaoReceitaDedutoraEstornada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaArrecadacaoReceitaDedutoraEstornada()
{
    parent::Persistente();
    $this->setTabela("tesouraria.arrecadacao_receita_dedutora_estornada");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_arrecadacao,cod_receita, cod_receita_dedutora, exercicio, timestamp_arrecadacao, timestamp_dedutora_estornada');

    $this->AddCampo('cod_arrecadacao'       , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('cod_receita'           , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('cod_receita_dedutora'  , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('exercicio'             , 'char'     , true , '4'   , true  , true  );
    $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true , ''    , true  , true  );
    $this->AddCampo('timestamp_dedutora_estornada', 'timestamp', true, ''  , true  , false );
    $this->AddCampo('timestamp_estornada'   , 'timestamp', true, ''  , true  , false );
    $this->AddCampo('vl_estornado'          , 'numeric'  , false, '14.2', false , false );

}

}
