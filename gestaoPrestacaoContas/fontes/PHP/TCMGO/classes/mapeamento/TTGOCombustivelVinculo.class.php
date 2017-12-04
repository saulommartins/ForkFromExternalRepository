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
    * Classe de mapeamento da tabela tcmgo.combustivel_vinculo
    * Data de Criação: 16/12/2008

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOCombustivelVinculo extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TTGOCombustivelVinculo()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.combustivel_vinculo");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_combustivel, cod_tipo, cod_item');

        $this->AddCampo('cod_combustivel' , 'integer' ,true, ''  , true , true );
        $this->AddCampo('cod_tipo'        , 'integer' ,true, ''  , true , true );
        $this->AddCampo('cod_item'        , 'integer' ,true, ''  , true , true );
    }

}
