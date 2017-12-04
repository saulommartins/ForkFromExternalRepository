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
    * @author Analista: Gelson
    * @author Desenvolvedor:

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 27/03/2012

  * @author Analista: Gelson
  * @author Desenvolvedor:

*/
class TTCMGOProcessos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOProcessos()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.processos");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_empenho,cod_entidade,exercicio');

        $this->AddCampo( 'cod_empenho'             , 'integer'       , true  , ''    , true  , true  );
        $this->AddCampo( 'cod_entidade'            , 'integer'       , true  , ''    , true  , true  );
        $this->AddCampo( 'exercicio'               , 'char'          , true  , '4'   , true  , true  );
        $this->AddCampo( 'numero_processo'         , 'char'          , false , '8'   , false , false );
        $this->AddCampo( 'exercicio_processo'      , 'char'          , false , '4'   , false , false );
        $this->AddCampo( 'processo_administrativo' , 'char'          , false , '20'  , false , false );
    }
}
