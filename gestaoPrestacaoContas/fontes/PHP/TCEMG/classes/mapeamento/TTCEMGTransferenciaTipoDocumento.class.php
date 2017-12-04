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
    * Classe de mapeamento da tabela TESOURARIA.TRANSFERENCIA_TIPO_DOCUMENTO
    * Data de Criação: 15/07/2016

    * @author Analista: Valtair
    * @author Desenvolvedor: Lisiane da Rosa Morais

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );


class TTCEMGTransferenciaTipoDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("tcemg.transferencia_tipo_documento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,exercicio,cod_lote,tipo,cod_tipo_documento');

    $this->AddCampo('cod_tipo_documento'        , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('cod_entidade'              , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio'                 , 'varchar'  , true, '04', true  , true  );
    $this->AddCampo('cod_lote'                  , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('tipo'                      , 'varchar'  , true, '1' , true  , true  );
    $this->AddCampo('num_documento'             , 'varchar'  , true, '15', false , false );
}

public function __destruct(){}

}
