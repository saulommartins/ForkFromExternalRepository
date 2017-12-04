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
* Classe de negócio Apensamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4339 $
$Name$
$Author: lizandro $
$Date: 2005-12-27 11:38:13 -0200 (Ter, 27 Dez 2005) $

Casos de uso: uc-01.06.98
*/

class apensamento
{
    /*******************************/
    /* Propriedades públicas       */
    /*******************************/

    /*******************************/
    /* Método Construtor           */
    /*******************************/

    /*******************************/
    /* Métodos Públicos            */
    /*******************************/
    public function incluiApensamentos($codProcesso, $aPensamentos)
    {
        $bOK = false;
        if (is_array($aPensamentos)) {
            $aProc = explode("-",$codProcesso);
            $iCodProcesso = $aProc[0];
            $sExeProcesso = $aProc[1];
            $sDataHora = hoje(true)." ".agora(false,true);

            $dbApensar = new dataBaseLegado;
            $dbApensar->comBegin = false;
            $dbApensar->abreBd();
            $dbApensar->executaSql("BEGIN");

            while (list($vKey,$vValor) = each($aPensamentos)) {
                $aCod = explode("_",$vValor);
                $sInsert = " insert into sw_processo_apensado
                            (cod_processo_pai,   exercicio_pai,cod_processo_filho, exercicio_filho,
                             timestamp_apensamento) values
                            (".$iCodProcesso.", '".$sExeProcesso."',".$aCod[0].",'".$aCod[1]."','".$sDataHora."');";
                $sInsert .= " update sw_processo
                                set cod_situacao = 4
                              where cod_processo = ".$aCod[0]."  AND
                                    ano_exercicio = '".$aCod[1]."'";
                //echo $sInsert."<br>";die();
                $dbApensar->executaSql($sInsert);
            }
            $bOK = $dbApensar->executaSql("COMMIT");
            $dbApensar->fechaBd();
        }

        return $bOK;
    }

    public function incluiDesapensamentos($codProcesso, $aDesapensamentos)
    {
        $bOK = false;
        if (is_array($aDesapensamentos)) {
            $aProc = explode("-",$codProcesso);
            $iCodProcesso = $aProc[0];
            $sExeProcesso = $aProc[1];
            $sDataHora = hoje(true)." ".agora(false,true);

            $dbDesapensar = new dataBaseLegado;
            $dbDesapensar->comBegin = false;
            $dbDesapensar->abreBd();
            $dbDesapensar->executaSql("BEGIN");

            while (list($vKey,$vValor) = each($aDesapensamentos)) {

                $aCod = explode("_",$vValor);
                $sUpdate = " UPDATE sw_processo_apensado
                                SET timestamp_desapensamento = '".$sDataHora."'
                              WHERE cod_processo_pai         =  ".$iCodProcesso."  AND
                                    exercicio_pai            = '".$sExeProcesso."' AND
                                    cod_processo_filho       =  ".$aCod[0]."       AND
                                    exercicio_filho          = '".$aCod[1]."'      AND
                                    timestamp_apensamento    = '".$aCod[2]."';";
                $sUpdate .= " update sw_processo
                                set cod_situacao = 3
                              where cod_processo = ".$aCod[0]."  AND
                                    ano_exercicio = '".$aCod[1]."'";
                //echo $sUpdate."<br>";die();
                $dbDesapensar->executaSql($sUpdate);
            }
            $bOK = $dbDesapensar->executaSql("COMMIT");
            $dbDesapensar->fechaBd();
        }

        return $bOK;
    }
}
