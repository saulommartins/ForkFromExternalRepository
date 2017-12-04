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
    * Arquivo de label de CGM
    * Data de Criação: 04/10/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.04.01
                    uc-03.04.02
*/

/*$Log$
 *Revision 1.3  2007/03/12 20:48:24  hboaventura
 *correção de problema com escapes
 *
/*Revision 1.2  2006/10/05 09:41:51  bruce
/*colocado UC e tag de log
/*
*/

include_once ( CLA_LABEL );

class  ILabelEditObjeto extends Label
{
    public $inNumCGM;

    public function ILabelCGM()
    {
        parent::Label();
        $this->setRotulo('Objeto');
    }

    public function setCodObjeto($inValor) { $this->inCodObjeto = $inValor ; }

    public function montaHTML()
    {
        if ( !$this->getValue() ) {
            include_once(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");
            $obComprasObjeto = new TComprasObjeto();
            $obComprasObjeto->setDado('cod_objeto', $this->inCodObjeto );
            $obComprasObjeto->recuperaPorChave($rsRecordSet);

            if ($rsRecordSet->getCampo('cod_objeto') != '') {
                $this->setValue( $rsRecordSet->getCampo('cod_objeto').' - '.stripslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSet->getCampo('descricao') )))) );
            }

        }
        parent::montaHTML();
    }
}
?>
