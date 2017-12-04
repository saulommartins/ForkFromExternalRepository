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
* Componente ISelectModalidadeLicitacao

* Data de Criação: 03/10/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

Casos de uso: uc-03.05.15

*/

include_once ( CLA_SELECT );

class ISelectModalidadeLicitacao extends Select
{
    public function ISelectModalidadeLicitacao()
    {
        parent::Select();

        include_once(CAM_GP_COM_MAPEAMENTO . "TComprasModalidade.class.php");
        $obMapeamento   = new TComprasModalidade();
        $rsRecordSet    = new Recordset;
        //$stFiltro = " WHERE cod_modalidade NOT IN(4,5,6,7)  ";
        $stFiltro = " WHERE cod_modalidade NOT IN(4,5)  ";

        $obMapeamento->recuperaTodos($rsRecordSet,$stFiltro,' ORDER BY descricao');

        $this->setRotulo            ("Modalidade"                            );
        $this->setName              ("inCodModalidade"                       );
        $this->setTitle             ("Selecione a modalidade de licitação."  );
        $this->setNull              (true                                    );
        $this->addOption            ("","Selecione"                          );
        $this->setCampoID           ("cod_modalidade"                        );
        $this->setCampoDesc         ("[cod_modalidade] - [descricao]"        );
        $this->preencheCombo        ($rsRecordSet                            );
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
