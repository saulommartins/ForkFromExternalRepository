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
     * Classe de mapeamento para a tabela IMOBILIARIO.atributo_tipo_licenca_lote_valor
     * Data de Criação: 25/03/2008

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMAtributoTipoLicencaLoteValor.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicenca.class.php" );

class TCIMAtributoTipoLicencaLoteValor extends PersistenteAtributosValores
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMAtributoTipoLicencaLoteValor()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.atributo_tipo_licenca_lote_valor');
        $this->setPersistenteAtributo( new TCIMAtributoTipoLicenca );
        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca,exercicio,cod_lote,cod_tipo,cod_modulo,cod_cadastro,cod_atributo,timestamp');

        $this->AddCampo( 'cod_licenca', 'integer', true, '', true, true );
        $this->AddCampo( 'exercicio', 'varchar', true, '4', true, true );
        $this->AddCampo( 'cod_lote', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_modulo', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_cadastro', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_atributo', 'integer', true, '', true, true );
        $this->AddCampo( 'valor', 'varchar', true, '500', false, false );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
    }
}
