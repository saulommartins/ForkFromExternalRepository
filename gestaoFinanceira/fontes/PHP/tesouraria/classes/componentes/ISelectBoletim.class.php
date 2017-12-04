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
* Componente ISelectBoletim

* Data de Criação: 19/10/2006

* @author Analista: Lucas Teixeiera Stephanou
* @author Desenvolvedor: Lucas Teixeiera Stephanou

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-07-13 13:12:34 -0300 (Sex, 13 Jul 2007) $

Casos de uso: uc-02.03.03 , uc-02.04.05,uc-02.04.17,uc-02.04.06,uc-02.04.20, uc-02.04.02 , uc-02.04.25

$Log$
Revision 1.5  2007/07/13 16:12:34  luciano
Bug#9631#,Bug#9632#,Bug#9633#

*/

include_once ( CLA_SELECT );

class ISelectBoletim extends Select
{
    public $obBoletim;
    public $rsRecordSet;
    public $stOrdem;

    public function setOrdenacao($valor) { $this->stOrdem = $valor; }
    public function getOrdenacao() { return $this->stOrdem; }

    public function ISelectBoletim()
    {
        parent::Select();

        include_once(CAM_GF_TES_NEGOCIO . "RTesourariaBoletim.class.php");
        $this->obBoletim      =  new RTesourariaBoletim();

        $this->setRotulo            ( "Boletim"                             );
        $this->setName              ( "inCodBoletim"                        );
        $this->setId                ( "inCodBoletim"                        );
        $this->setTitle             ( "Selecione o Boletim a ser usado."    );
        $this->setNull              ( false                                 );

        if (!$this->stOrdem) {
            $this->stOrdem = " cod_boletim ";
        }
    }

    public function montaHTML()
    {
        $this->rsRecordSet =  new Recordset ;
        $this->obBoletim->listarBoletimAberto ( $this->rsRecordSet, $this->stOrdem );

        $boMonta = TRUE;
        if ( (integer) $this->rsRecordSet->getNumLinhas() == 1 ) {
            $this->addOption            ( $this->rsRecordSet->getCampo( 'cod_boletim' ). ":" . $this->rsRecordSet->getCampo( 'dt_boletim' ).":".$this->rsRecordSet->getCampo( 'exercicio' ).":".$this->rsRecordSet->getCampo('cod_entidade') , $this->rsRecordSet->getCampo( 'cod_boletim' ) . ' - ' . $this->rsRecordSet->getCampo( 'dt_boletim' ) );
            $this->setReadOnly          ( true );
        } elseif ( (integer) $this->rsRecordSet->getNumLinhas() > 1 ) {
            $this->addOption            ( "","Selecione"                          );
            $this->setCampoID           ( "[cod_boletim]:[dt_boletim]:[exercicio]:[cod_entidade]" );
            $this->setCampoDesc         ( "[cod_boletim] - [dt_boletim]"          );
            $this->preencheCombo        ( $this->rsRecordSet                            );
        } else {
            $boMonta = FALSE;
        }

        if ( $boMonta )
            parent::montaHTML();
        else
            $this->setHTML( "Não há boletins abertos para esta entidade!" );
    }
}
?>
