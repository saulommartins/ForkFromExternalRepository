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
    * Classe de mapeamento da tabela TESOURARIA_BOLETIM_LIBERADO
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-07-13 16:18:44 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.04.04,uc-02.04.17
*/

/*
$Log$
Revision 1.8  2007/07/13 19:10:48  cako
Bug#9383#, Bug#9384#

Revision 1.7  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_BOLETIM_LIBERADO
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaBoletimLiberado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaBoletimLiberado()
{
    parent::Persistente();
    $this->setTabela("tesouraria.boletim_liberado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_boletim,exercicio,cod_entidade,timestamp_liberado,timestamp_fechamento');

    $this->AddCampo('cod_boletim'          , 'integer'  , true ,''  , true  , true  );
    $this->AddCampo('exercicio'            , 'varchar'  , true ,'04', true  , true  );
    $this->AddCampo('cod_entidade'         , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('timestamp_liberado'   , 'timestamp', true, ''  , true  , false );
    $this->AddCampo('timestamp_fechamento' , 'timestamp', true, ''  , true  , true  );
    $this->AddCampo('cod_terminal'         , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal'   , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'    , 'timestamp', true, ''  , false , true  );
}

}
