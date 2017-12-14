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
    * Classe de mapeamento da tabela TCMGO.TIPO_DOCUMENTO
    * Data de Criação: 12/01/2009

    * @author Analista: Tonismar Régis Bernardo
    * @author Desenvolvedor: Lucas Andrades Mendes

    * @package URBEM
    * @subpackage Mapeamento

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMGO.TIPO_DOCUMENTO
  * Data de Criação: 12/01/2009

  * @author Analista: Tonismar
  * @author Desenvolvedor: Lucas Andrades Mendes

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoTipoDocumentoTcemgInterna extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoTipoDocumentoTcemgInterna()
    {
        parent::Persistente();
        $this->setTabela('tcemg.tipo_documento');

        $this->setCampoCod('cod_tipo');

        $this->AddCampo( 'cod_tipo','integer',false, '', true   ,false  );
        $this->AddCampo( 'descricao','char'  ,false, '35',false ,false );
    }
}
